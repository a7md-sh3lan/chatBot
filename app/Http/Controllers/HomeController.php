<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Guest;
use App\Category;
use App\SubCategory;
use App\Element;
use App\Log;
use Carbon\Carbon;

class HomeController extends Controller
{
    //
    public function home(Request $request){
        // $session = $request->session();
        // $session->put('my_name','Virat Gandhi');
        return view('welcome', compact(''));
    }

    public function checkGuest(Request $request){
        $guest = Guest::where('phone_number', $request->phone_number)->first();
        $categories = Category::all();
        $initial = (object)['phase' => 'category', 'previous_phase' => null, 'previous_option', null];
        if($guest) {
            if($guest->logs && (Carbon::now())->diffInMinutes($guest->logs->first()->updated_at, true) < 30) {
                $options = unserialize($guest->logs->first()->messages);
                if($options['phase'] == 'category') {
                    $target = Category::all();
                } elseif($options['phase'] == 'sub category') {
                    $target = SubCategory::where('category_id', $options['previous_option'])->get();
                    if($target->count() < 1) {
                        $target = Element::where(['followable_type' => 'App\Category','followable_id' => $options['previous_option']])->get();
                    } else if(!$target) {
                        $target = null;
                    }
                } elseif($options['phase'] == 'element') {
                    if($options['previous_phase'] == 'category') {
                        $type = 'App\Category';
                    } elseif($options['previous_phase'] == 'sub category') {
                        $type = 'App\SubCategory';
                    }
                    $target = Element::where(['followable_type' => $type,'followable_id' => $options['previous_option']])->get();
                } elseif($options['phase'] == 'confirm') {
                    $target = Element::where('id', $options['previous_option'])->first();
                } elseif($options['phase'] == 'payment') {
                    $target = Element::where('id', $options['previous_option'])->first();
                }
                return response()->json(['session' => true, 'status' => true, 'previous_phase' => $options['previous_phase'], 'phase' => $options['phase'], 'target' => $target ? $target : null], 201);
            } else {
                Log::create(['guest_id' => $guest->id, 'messages' => serialize((array)$initial)]);
                return response()->json(['session' => false, 'status' => true, 'categories' => $categories, 'phase' => 'category'], 202);
            }
        }
        $guest = Guest::create(['name' => $request->name, 'phone_number' => $request->phone_number]);
        Log::create(['guest_id' => $guest->id, 'messages' => serialize((array)$initial)]);
        return response()->json(['status' => false, 'categories' => $categories, 'phase' => 'category'], 203);
    }

    public function sendToBot(Request $request) {
        $guest = Guest::where('phone_number', $request->phone)->first();
        $log   = $guest->logs->first();
        if($request->order === '0' || strtolower($request->order) == 'back') {
            $newTarget = Category::all();
            $nextPhase = 'category';
            $newLog = (object)['phase' => $nextPhase, 'previous_phase' => null, 'previous_option' => null];
            $log->update(['messages' => serialize((array)$newLog)]);
            return response()->json(['status' => true, 'phase' => $nextPhase, 'target' => $newTarget], 200);
        }
        if($request->phase == 'category') {
            $target = Category::where('id', $request->order)->orWhere(strtolower('name'), strtolower($request->order))->first();
            if(!$target) {
                return response()->json(['status' => false, 'phase' => 'category'], 202);
            } 
            if($target->subCategories->count()){
                $newTarget = $target->subCategories;
                $nextPhase = 'sub category';
            } elseif($target->elements->count()) {
                $newTarget = $target->elements;
                $nextPhase = 'element';
            } else {
                $newTarget = false;
                $nextPhase = 'category';
            };
            $newLog = (object)['phase' => $nextPhase, 'previous_phase' => 'category', 'previous_option' => $target ? $target->id : null];
        } elseif($request->phase == 'sub category') {
            $target = SubCategory::where('id', $request->order)->orWhere(strtolower('name'), strtolower($request->order))->first();
            if(!$target) {
                return response()->json(['status' => false, 'phase' => 'sub category'], 202);
            } 
            if($target->elements->count()) {
                $newTarget = $target->elements;
                $nextPhase = 'element';
            } else {
                $newTarget = false;
                $nextPhase = 'sub category';
            };
            $newLog = (object)['phase' => $nextPhase, 'previous_phase' => 'sub category', 'previous_option' => $target ? $target->id : null];
        } elseif($request->phase == 'element') {
            $target = Element::where('id', $request->order)->orWhere(strtolower('name'), strtolower($request->order))->first();
            if(!$target) {
                return response()->json(['status' => false, 'phase' => 'element'], 202);
            } 
            if($target->count()) {
                $newTarget = $target;
                $nextPhase = 'confirm';
            } else {
                $newTarget = false;
                $nextPhase = 'element';
            };
            $newLog = (object)['phase' => $nextPhase, 'previous_phase' => 'element', 'previous_option' => $target ? $target->id : null];
        } elseif($request->phase == 'confirm') {
            $options = unserialize($guest->logs->first()->messages);
            $target = Element::where('id', $options['previous_option'])->first();
            if($request->order === '1' || strtolower($request->order) == 'ok') {
                $newLog = (object)['phase' => 'category', 'previous_phase' => 'category', 'previous_option' => 'payement'];
                $log->update(['messages' => serialize((array)$newLog)]);
                return response()->json(['status' => true, 'phase' => 'payement', 'target' => $target], 200);
            } else {
                $newTarget = false;
                $nextPhase = 'confirm';
            };
            $newLog = (object)['phase' => $nextPhase, 'previous_phase' => 'confirm', 'previous_option' => $target ? $target->id : null];
        }
        $log->update(['messages' => serialize((array)$newLog)]);
        return response()->json(['status' => true, 'phase' => $nextPhase, 'target' => $newTarget], 200);
    }
}
