<?php


namespace App\Helpers;

use App\Models\Cart as CartModel;
use App\Models\Coupon;
use App\Models\Product;
use App\Values\SetValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use stdClass;

class Cart
{
    private static $cart;
    private static $user;

    public static function add($productId, $qty = 1)
    {
        $product = self::init($productId);
        $product->qty = $qty;

        self::addOrReplace($product);
        self::recalculate();
        return self::finish();
    }

    public static function remove($productId)
    {
        $product = self::init($productId);

        self::removeIfExists($product);
        self::recalculate();
        return self::finish();
    }

    public static function increase($productId)
    {
        $product = self::init($productId);

        self::increment($product);
        self::recalculate();
        return self::finish();
    }

    public static function decrease($productId)
    {
        $product = self::init($productId);

        self::decrement($product);
        self::recalculate();
        return self::finish();
    }

    public static function applyCoupon($code)
    {
        self::init();
        self::checkAndApplyCoupon($code);

        self::recalculate();
        return self::finish();
    }

    public static function products()
    {
        self::init();
        return self::$cart->items;
    }

    public static function qty()
    {
        self::init();
        return self::$cart->qty;
    }

    public static function subTotal()
    {
        self::init();
        return self::$cart->sub_total;
    }

    public static function tax()
    {
        self::init();
        return self::$cart->tax;
    }

    public static function shipping()
    {
        self::init();
        return self::$cart->shipping;
    }

    public static function discount()
    {
        self::init();
        return self::$cart->discount;
    }

    public static function grandTotal()
    {
        self::init();
        return self::$cart->grand_total;
    }
    
    public static function vendor()
    {
        self::init();
        return self::$cart->vendor;
    }
 
    public static function has($id)
    {
        self::init();
        return self::find($id);
    }
    
    public static function exists()
    {
        self::init();
        return self::$cart->qty > 0;
    }

    public static function clear()
    {
        self::clearFromSession();
        self::clearFromDatabase();
    }

/**********Private Functions*************/
    private static function init($productId = false)
    {
        self::authInit();
        if (self::$user)
            self::initCartFromDatabase();
          
        else
            self::initCartFromSession();

        if ($productId)
            return Product::find($productId);
    }

    private static function authInit()
    {
        self::$user = Auth::user();
    }

    private static function initCartFromSession()
    {
        if (Session::has('cart'))
            self::$cart = Session::get('cart');
        else
            self::initEmptyCart();
    }

    private static function initCartFromDatabase()
    {
        $dbCart = self::$user->cart;
        if ($dbCart)
            self::parseCartFromDatabase($dbCart);
        else if (Session::has('cart'))
            self::initCartFromSession();
        else
            self::initEmptyCart();
    }

    private static function initEmptyCart()
    {
        $cart = new stdClass;
        $cart->items = [];
        $cart->qty = 0;
        $cart->discount_rate = 0;
        $cart->coupon_success = false;
        $cart->shipping = 0;
        $cart->discount = 0;
        $cart->sub_total = 0;
        $cart->grand_total = 0;
        $cart->vendor = null;
        self::$cart = $cart;
    }

    private static function parseCartFromDatabase($dbCart)
    {
        $cart = new stdClass;
        $cart->items = [];

        foreach ($dbCart->getItems() as $item) {
            $product = Product::find($item->id);
            $product->qty = $item->qty;
            $cart->items[] = $product;
        }

        $cart->qty = $dbCart->qty;
        $cart->discount_rate = $dbCart->discount_rate;
        $cart->coupon_success = $dbCart->coupon_success;
        $cart->tax = $dbCart->tax;
        $cart->shipping = $dbCart->shipping;
        $cart->discount = $dbCart->discount;
        $cart->sub_total = $dbCart->sub_total;
        $cart->grand_total = $dbCart->grand_total;
        $cart->vendor = $dbCart->vendor;

        self::$cart = $cart;
    }

    private static function finish()
    {
        if(self::$user)
            self::saveCartToDatabase();
        else
            self::saveCartToSession();

        $response = new Api();
        $response->add('qty', self::$cart->qty);
        $response->add('discountRate', self::$cart->discount_rate);
        $response->add('couponSuccess', self::$cart->coupon_success);
        $response->add('discount', self::$cart->discount);
        $response->add('tax', self::$cart->discount);
        $response->add('subTotal', self::$cart->sub_total);
        $response->add('grandTotal', self::$cart->grand_total);
        $response->add('vendor', self::$cart->vendor);
        return $response;
    }

    private static function saveCartToSession()
    {
        if (self::$cart->qty < 1)
            Session::forget('cart');
        else
            Session::put('cart', self::$cart);

    }

    private static function saveCartToDatabase()
    {
        $items = [];
        foreach(self::$cart->items as $item){
            $obj = new stdClass;
            $obj->id = $item->id;
            $obj->qty = $item->qty;
            $items[] = $obj;
        }
        
        $items = json_encode($items);
        
        CartModel::updateOrCreate(
            ['user_id' => self::$user->id],
            [
                'items' => $items,
                'qty' => self::$cart->qty,
                'discount_rate' => self::$cart->discount_rate,
                'coupon_success' => self::$cart->coupon_success,
                'shipping' => self::$cart->shipping,
                'discount' => self::$cart->discount,
                'sub_total' => self::$cart->sub_total,
                'grand_total' => self::$cart->grand_total,
                'vendor_id' => self::$cart->vendor->id,
            ]);
    }

    private static function clearFromSession(){
        Session::forget('cart');
    }
    
    private static function clearFromDatabase(){
        if(Auth::check()){
           $cart =  Auth::user()->cart;
           if($cart)
            $cart->delete();
        }
    }

    private static function recalculate()
    {
        self::$cart->sub_total = 0;
        self::$cart->qty = 0;
        self::$cart->shipping = 0;
        foreach (self::$cart->items as $item) {
            self::$cart->sub_total += ($item->price * $item->qty);
            self::$cart->shipping += ($item->shipping * $item->qty);
            self::$cart->qty += $item->qty;
        }

        self::$cart->discount = round(self::$cart->sub_total * self::$cart->discount_rate / 100);
        self::$cart->grand_total = self::$cart->sub_total  + self::$cart->shipping - self::$cart->discount;
    }

    private static function find($id)
    {
        foreach (self::$cart->items as $item) {
            if ($item->id == $id) {
                return true;
            }
        }
        return false;
    }
    
    private static function addOrReplace($product)
    {
        $found = false;
        foreach (self::$cart->items as $key => $item) {
            if ($item->id == $product->id) {
                $found = true;
                self::$cart->items[$key] = $product;
                break;
            }
        }
        if (!$found){
            if(self::$cart->vendor)
                if(self::$cart->vendor->id != $product->vendor->id)
                self::initEmptyCart();
            self::$cart->items[] = $product;
            self::$cart->vendor = $product->vendor;
        }
    }

    private static function removeIfExists($product)
    {
        foreach (self::$cart->items as $key => $item) {
            if ($item->id == $product->id) {
                unset(self::$cart->items[$key]);
            }
        }
    }

    private static function increment($product)
    {
        $found = false;
        foreach (self::$cart->items as $key => $item) {
            if ($item->id == $product->id) {
                $found = true;
                self::$cart->items[$key]->qty++;
                break;
            }
        }
        if (!$found) {
            if(self::$cart->vendor)
                if(self::$cart->vendor->id != $product->vendor->id)
                    self::initEmptyCart();
            $product->qty = 1;
            self::$cart->items[] = $product;
            self::$cart->vendor = $product->vendor;
        }
    }

    private static function decrement($product)
    {
        foreach (self::$cart->items as $key => $item) {
            if ($item->id == $product->id) {
                self::$cart->items[$key]->qty--;
                if (self::$cart->items[$key]->qty < 1) unset(self::$cart->items[$key]);
                break;
            }
        }
    }

    private static function checkAndApplyCoupon($code)
    {
        $coupon = Coupon::where('code', $code)->first();
        if ($coupon) {
            self::$cart->discount_rate = $coupon->rate;
            self::$cart->coupon_success = true;
        } else {
            self::$cart->discount_rate = 0;
            self::$cart->coupon_success = false;
        }
    }
    //End Private Functions//
}
