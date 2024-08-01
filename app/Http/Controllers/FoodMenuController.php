<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Menu;
use App\Models\FoodMenu;
use Illuminate\Http\Request;

class FoodMenuController extends Controller
{
    public function getFoodMenus()
    {
        $allFoodMenus = FoodMenu::all();

        if($allFoodMenus)
        {
            return response()->json([
                'status'  => true,
                'data'    => $allFoodMenus
            ], 200);
        }
        else
        {
            return response()->json([
                'status'  => true,
                'message' => 'No Record Was Found'
            ], 200);
        }
    }

    public function getSingleFoodMenu($id)
    {
        ////  CHECK IF FOOD MENU ID EXISTS
        if (FoodMenu::where('id', $id)->exists())
        {
            $singleFoodMenu = FoodMenu::findOrFail($id);

            return response()->json([
                'status'  => true,
                'data'    => $singleFoodMenu
            ], 200);
        }
        else
        {
            return response()->json([
                'status'  => false,
                'message' => 'FOOD Menu with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
            ], 404);
        }
    }

    public function discount()
    {
        $foodkDiscount = FoodMenu::where('discount', '!=', null)->get();

        if (count($foodkDiscount) > 0)
        {
            return response()->json([
                'status'  => true,
                'count'   => count($foodkDiscount),
                'data'    => $foodkDiscount
            ]);
        }
        else
        {
            return response()->json([
                'status'    => true,
                'message'   => 'No Food With Discount For Now...'
            ]);
        }
    }

    public function saveFoodMenu(Request $request)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            /// VALIDATE FOOD MENU PROPERTIES
            $validator = Validator::make(request()->all(), [
                'name'  => 'required|unique:food_menus',
                'price' => 'required'
            ]);

            if($validator->fails())
            {
                return response()->json($validator->errors()->toJson(), 400);
            }

            try
            {
                //// INSTANTIATE FOOD MENU AND CREATE NEW FOOD MENUS
                $createFoodMenu = new FoodMenu();
                $createFoodMenu->user_id = auth()->user()->id;

                if (Menu::where('id', $request->menu_id)->exists())
                {
                    $createFoodMenu->menu_id = $request->menu_id;
                    $createFoodMenu->name = $request->name;
                    $createFoodMenu->price = $request->price;
                    $createFoodMenu->discount = $request->discount;
                    $createFoodMenu->description = $request->description;

                    if ($createFoodMenu->discount !== "" || $createFoodMenu->discount !== null)
                    {
                        $createFoodMenu->new_price = $createFoodMenu->price - $createFoodMenu->discount;
                    }

                    if($createFoodMenu->save())
                    {
                        return response()->json([
                            'status'  => true,
                            'message' => 'Food Menu Was Created Successfully!',
                            'data'    => $createFoodMenu
                        ], 201);
                    }
                    else
                    {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Failed! Food Menu Was Not Created'
                        ], 400);
                    }
                }
                else
                {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Menu with the ID of ' . '(' . $request->menu_id . ')' . ' Does Not Exist'
                    ], 404);
                }
            }
            catch (\Throwable $th)
            {
                //throw $th;
                return response()->json([
                    'status'  => false,
                    'message' => 'Oooooop! Something Went Wrong...',
                    'data'    => $th->getMessage()
                ], 500);
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

    public function updateFoodMenu(Request $request, $id)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            try
            {
                ////  CHECK IF FOOD MENU ID EXISTS
                if (FoodMenu::where('id', $id)->exists())
                {
                    //// //// //// UPDATE FOOD MENU
                    $updateFoodMenu = FoodMenu::findOrFail($id);
                    $updateFoodMenu->user_id = auth()->user()->id;

                    if (Menu::where('id', $request->menu_id)->exists())
                    {
                        $updateFoodMenu->menu_id = $request->menu_id;
                        $updateFoodMenu->name = $request->name;
                        $updateFoodMenu->price = $request->price;
                        $updateFoodMenu->discount = $request->discount;
                        $updateFoodMenu->description = $request->description;

                        /// CHECK IF USER IS TRYING TO SUBMIT AN EMPTY FIELD
                        if ($updateFoodMenu->name === "" || $updateFoodMenu->name === null)
                        {
                            return response()->json([
                                'status'  => false,
                                'message' => 'Name Field Can Not Be Empty!'
                            ], 400);
                        }

                        if ($updateFoodMenu->price === "" || $updateFoodMenu->price === null)
                        {
                            return response()->json([
                                'status'  => false,
                                'message' => 'Price Field Can Not Be Empty!'
                            ], 400);
                        }

                        if ($updateFoodMenu->discount !== "" || $updateFoodMenu->discount !== null)
                        {
                            $updateFoodMenu->new_price = $updateFoodMenu->price - $updateFoodMenu->discount;
                        }

                        if($updateFoodMenu->save())
                        {
                            return response()->json([
                                'status'  => true,
                                'message' => 'Food Menu Was Created Successfully!',
                                'data'    => $updateFoodMenu
                            ], 200);
                        }
                        else
                        {
                            return response()->json([
                                'status'  => false,
                                'message' => 'Failed! Food Menu Was Not Created'
                            ], 400);
                        }
                    }
                    else
                    {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Menu with the ID of ' . '(' . $request->menu_id . ')' . ' Does Not Exist'
                        ], 404);
                    }
                }
                else
                {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Food Menu with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
                    ], 404);
                }
            }
            catch (\Throwable $th)
            {
                //throw $th;
                return response()->json([
                    'status'  => false,
                    'message' => 'Oooooop! Something Went Wrong...',
                    'data'    => $th->getMessage()
                ], 500);
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

    public function deleteFoodMenu($id)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            ////  CHECK IF FOOD MENU ID EXISTS
            if (FoodMenu::where('id', '=', $id)->exists())
            {
                $deleteFoodMenu = FoodMenu::findOrFail($id);
                $deleteFoodMenu->delete();

                return response()->json([
                    'status'  => true,
                    'message' => 'Food Menu Was Deleted Successfully!'
                ], 200);
            }
            else
            {
                return response()->json([
                    'status'  => false,
                    'message' => 'Food Menu with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
                ], 404);
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
}
