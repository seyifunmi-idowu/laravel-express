<?php
namespace App\Services;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\CustomAPIException;

class CustomerAddressService
{
    public static function createCustomerAddress($user, $request)
    {
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            throw new CustomAPIException('Customer not found', 404);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $direction = $request->direction;
        $landmark = $request->landmark;
        $label = $request->label;

        $addressInfo = MapService::getInfoFromLatitudeAndLongitude($latitude, $longitude);

        if (empty($addressInfo)) {
            throw new CustomAPIException('Unable to locate address', 404);
        }
        $address = Address::create([
            'formatted_address' => $addressInfo[0]['formatted_address'],
            'customer_id' => $customer->id,
            'direction' => $direction,
            'landmark' => $landmark,
            'label' => $label,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'save_address' => false
        ]);

        return $address;
    }

    public static function getCustomerAddress($user)
    {
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            throw new CustomAPIException('Customer not found', 404);
        }

        return Address::where('customer_id', $customer->id)->get();
    }

    public static function deleteCustomerAddress($user, $addressId)
    {
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            throw new CustomAPIException('Customer not found', 404);
        }

        $address = Address::where(['id'=> $addressId, 'customer_id'=> $customer->id])->first();
        if (!$address) {
            throw new CustomAPIException('Address not found', 404);
        }

        $address->delete();
        return true;
    }

    public static function updateCustomerAddress($user, $addressId, $request)
    {
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            throw new CustomAPIException('Customer not found', 404);
        }

        $address = Address::where(['id'=> $addressId, 'customer_id'=> $customer->id])->first();
        if (!$address) {
            throw new CustomAPIException('Address not found', 404);
        }
    
        $address->formatted_address = $request->formatted_address ?? $address->formatted_address;
        $address->direction = $request->direction ?? $address->direction;
        $address->landmark = $request->landmark ?? $address->landmark;
        $address->label = $request->label ?? $address->label;
        $address->save();
        return $address;
    }
}
