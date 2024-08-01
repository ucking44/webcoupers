<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Menu;
use App\Models\DrinkMenu;
use Illuminate\Http\Request;

class DrinkMenuController extends Controller
{
    public function getDrinkMenus()
    {
        $allDrinkMenus = DrinkMenu::all();

        if(count($allDrinkMenus))
        {
            return response()->json([
                'status'  => true,
                'data'    => $allDrinkMenus
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

    public function getSingleDrinkMenu($id)
    {
        ////  CHECK IF DRINK MENU ID EXISTS
        if (DrinkMenu::where('id', $id)->exists())
        {
            $singleDrinkMenu = DrinkMenu::findOrFail($id);

            return response()->json([
                'status'  => true,
                'data'    => $singleDrinkMenu
            ], 200);
        }
        else
        {
            return response()->json([
                'status'  => false,
                'message' => 'Drink Menu with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
            ], 404);
        }
    }

    public function discount()
    {
        $drinkDiscount = DrinkMenu::where('discount', '!=', null)->get();

        if (count($drinkDiscount) > 0)
        {
            return response()->json([
                'status'  => true,
                'count'   => count($drinkDiscount),
                'data'    => $drinkDiscount
            ]);
        }
        else
        {
            return response()->json([
                'status'    => true,
                'message'   => 'No Drink With Discount For Now...'
            ]);
        }
    }

    public function saveDrinkMenu(Request $request)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            /// VALIDATE DRINK MENU PROPERTIES
            $validator = Validator::make(request()->all(), [
                'name'  => 'required|unique:drink_menus',
                'price' => 'required'
            ]);

            if($validator->fails())
            {
                return response()->json($validator->errors()->toJson(), 400);
            }

            try
            {
                //// INSTANTIATE DRINK MENU AND CREATE NEW DRINK MENUS
                $createDrinkMenu = new DrinkMenu();
                $createDrinkMenu->user_id = auth()->user()->id;

                if (Menu::where('id', $request->menu_id)->exists())
                {
                    $createDrinkMenu->menu_id = $request->menu_id;
                    $createDrinkMenu->name = $request->name;
                    $createDrinkMenu->price = $request->price;
                    $createDrinkMenu->discount = $request->discount;
                    $createDrinkMenu->description = $request->description;

                    if ($createDrinkMenu->discount !== "" || $createDrinkMenu->discount !== null)
                    {
                        $createDrinkMenu->new_price = $createDrinkMenu->price - $createDrinkMenu->discount;
                    }

                    if($createDrinkMenu->save())
                    {
                        return response()->json([
                            'status'  => true,
                            'message' => 'Drink Menu Was Created Successfully!',
                            'data'    => $createDrinkMenu
                        ], 201);
                    }
                    else
                    {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Failed! Drink Menu Was Not Created'
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

    public function updateDrinkMenu(Request $request, $id)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            try
            {
                ////  CHECK IF DRINK MENU ID EXISTS
                if (DrinkMenu::where('id', $id)->exists())
                {
                    //// //// //// UPDATE DRINK MENU
                    $updateDrinkMenu = DrinkMenu::findOrFail($id);
                    $updateDrinkMenu->user_id = auth()->user()->id;

                    ////  CHECK IF MENU ID EXISTS
                    if (Menu::where('id', $request->menu_id)->exists())
                    {
                        $updateDrinkMenu->menu_id = $request->menu_id;
                        $updateDrinkMenu->name = $request->name;
                        $updateDrinkMenu->price = $request->price;
                        $updateDrinkMenu->discount = $request->discount;
                        $updateDrinkMenu->description = $request->description;

                        /// CHECK IF USER IS TRYING TO SUBMIT AN EMPTY FIELD
                        if ($updateDrinkMenu->name === "" || $updateDrinkMenu->name === null)
                        {
                            return response()->json([
                                'status'  => false,
                                'message' => 'Name Field Can Not Be Empty!'
                            ], 400);
                        }

                        if ($updateDrinkMenu->price === "" || $updateDrinkMenu->price === null)
                        {
                            return response()->json([
                                'status'  => false,
                                'message' => 'Price Field Can Not Be Empty!'
                            ], 400);
                        }

                        if ($updateDrinkMenu->discount !== "" || $updateDrinkMenu->discount !== null)
                        {
                            $updateDrinkMenu->new_price = $updateDrinkMenu->price - $updateDrinkMenu->discount;
                        }

                        if($updateDrinkMenu->save())
                        {
                            return response()->json([
                                'status'  => true,
                                'message' => 'Drink Menu Was Created Successfully!',
                                'data'    => $updateDrinkMenu
                            ], 200);
                        }
                        else
                        {
                            return response()->json([
                                'status'  => false,
                                'message' => 'Failed! Drink Menu Was Not Created'
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
                        'message' => 'Drink Menu with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
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

    public function deleteDrinkMenu($id)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            ////  CHECK IF DRINK MENU ID EXISTS
            if (DrinkMenu::where('id', '=', $id)->exists())
            {
                $deleteDrinkMenu = DrinkMenu::findOrFail($id);
                $deleteDrinkMenu->delete();

                return response()->json([
                    'status'  => true,
                    'message' => 'Drink Menu Was Deleted Successfully!'
                ], 200);
            }
            else
            {
                return response()->json([
                    'status'  => false,
                    'message' => 'Drink Menu with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
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
