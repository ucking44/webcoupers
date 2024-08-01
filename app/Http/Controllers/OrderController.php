<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Menu;
use App\Models\Order;
use App\Models\FoodMenu;
use App\Models\DrinkMenu;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function placedOrder()
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            $getAllPlacedOrder = Order::get();

            if(count($getAllPlacedOrder))
            {
                return response()->json([
                    'status'  => true,
                    'data'    => $getAllPlacedOrder
                ], 200);
            }
            else
            {
                return response()->json([
                    'status'  => true,
                    'message' => 'No Placed Order Was Found'
                ], 200);
            }
        }
        else
        {
            return response()->json([
                'status'  => false,
                'message' => 'You do not have permission to access this resource'
            ], 500);
        }
    }

    public function sendOrder(Request $request)
    {
        /// VALIDATE DRINK MENU PROPERTIES
        $validator = Validator::make(request()->all(), [
            'phone'  => 'required|unique:orders',
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try
        {
            //// INSTANTIATE ORDER AND SEND NEW ORDER
            $sendOrder = new Order();
            $sendOrder->user_id = auth()->user()->id;
            ////  CHECK IF FOOD MENU ID EXISTS
            if (FoodMenu::where('id', $request->food_menu_id)->exists())
            {
                $sendOrder->food_menu_id = $request->food_menu_id;
            }
            else
            {
                return response()->json([
                    'status'  => false,
                    'message' => 'Food Menu with the ID of ' . '(' . $request->food_menu_id . ')' . ' Does Not Exist'
                ]);
            }
            ////  CHECK IF DRINK MENU ID EXISTS
            if (DrinkMenu::where('id', $request->drink_menu_id)->exists())
            {
                $sendOrder->drink_menu_id = $request->drink_menu_id;
            }
            else
            {
                return response()->json([
                    'status'  => false,
                    'message' => 'Drink Menu with the ID of ' . '(' . $request->drink_menu_id . ')' . ' Does Not Exist'
                ]);
            }

            $sendOrder->name = auth()->user()->name;
            $sendOrder->phone = $request->phone;
            $sendOrder->email = auth()->user()->email;
            $sendOrder->message = $request->message;

            if($sendOrder->save())
            {
                return response()->json([
                    'status'  => true,
                    'message' => 'Order Was Sent Successfully!',
                    'data'    => $sendOrder
                ], 201);
            }
            else
            {
                return response()->json([
                    'status'  => false,
                    'message' => 'Failed! Order Was Not Sent'
                ], 400);
            }
        }
        catch (\Throwable $th)
        {
            return response()->json([
                'status'  => false,
                'message' => 'Oooooop! Something Went Wrong...',
                'data'    => $th->getMessage()
            ], 500);
        }
    }
}
