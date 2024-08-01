<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function getAllMenus()
    {
        $allMenus = Menu::get();

        if($allMenus)
        {
            return response()->json([
                'status'  => true,
                'data'    => $allMenus
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

    public function getSingleMenu($id)
    {
        ////  CHECK IF MENU ID EXISTS
        if (Menu::where('id', $id)->exists())
        {
            $singleMenu = Menu::findOrFail($id);

            return response()->json([
                'status'  => true,
                'data'    => $singleMenu
            ], 200);
        }
        else
        {
            return response()->json([
                'status'  => false,
                'message' => 'Menu with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
            ], 404);
        }
    }

    public function saveMenu(Request $request)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            /// VALIDATE MENU PROPERTIES
            $validator = Validator::make(request()->all(), [
                'name' => 'required|unique:menus'
            ]);

            if($validator->fails())
            {
                return response()->json($validator->errors()->toJson(), 400);
            }

            try
            {
                //// INSTANTIATE MENU AND CREATE NEW MENUS
                $createMenu = new Menu();
                $createMenu->user_id = auth()->user()->id;
                $createMenu->name = $request->name;
                $createMenu->description = $request->description;
                $createMenu->save();

                if($createMenu)
                {
                    return response()->json([
                        'status'  => true,
                        'message' => 'Menu Was Created Successfully!',
                        'data'    => $createMenu
                    ], 201);
                }
                else
                {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Failed! Menu Was Not Created'
                    ], 400);
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

    public function updateMenu(Request $request, $id)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            try
            {
                ////  CHECK IF MENU ID EXISTS
                if (Menu::where('id', $id)->exists())
                {
                    //// //// UPDATE MENU
                    $updateMenu = Menu::findOrFail($id);
                    $updateMenu->user_id = auth()->user()->id;
                    $updateMenu->name = $request->name;
                    $updateMenu->description = $request->description;

                    /// CHECK IF USER IS TRYING TO SUBMIT AN EMPTY FIELD
                    if ($updateMenu->name === "" || $updateMenu->name === null)
                    {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Name Field Can Not Be Empty!'
                        ], 400);
                    }

                    if($updateMenu->save())
                    {
                        return response()->json([
                            'status'  => true,
                            'message' => 'Menu Was Updated Successfully!',
                            'data'    => $updateMenu
                        ], 200);
                    }
                    else
                    {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Failed! Menu Was Not Updated'
                        ], 400);
                    }
                }
                else
                {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Menu with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
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

    public function deleteMenu($id)
    {
        ///CHECK IF LOGGED USER HAVE THE RIGHT PERMISSION
        if (auth()->user()->usertype === "staff")
        {
            ////  CHECK IF MENU ID EXISTS
            if (Menu::where('id', '=', $id)->exists())
            {
                $deleteMenu = Menu::findOrFail($id);
                $deleteMenu->delete();

                return response()->json([
                    'status'  => true,
                    'message' => 'Menu Was Deleted Successfully!'
                ], 200);
            }
            else
            {
                return response()->json([
                    'status'  => false,
                    'message' => 'Menu with the ID of ' . '(' . $id . ')' . ' Does Not Exist'
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
