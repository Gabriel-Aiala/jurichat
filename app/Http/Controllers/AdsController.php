<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use DataTables;

class AdsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Ad::all();
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('actions', function($row){
                $actionBtn = '<div>                                            
                                    <a href="'. route("dashboard.admin.ads.edit", $row["id"] ). '"><i class="fa fa-solid fa-pencil-square table-action-buttons edit-action-button" title="Edit Ad"></i></a>
                                </div>';
                return $actionBtn;
            })
            ->addColumn('updated-on', function($row){
                $created_on = '<span>'.date_format($row["updated_at"], 'd M Y H:i A').'</span>';
                return $created_on;
            })
            ->addColumn('custom-type', function($row){
                $type = '<span class="font-weight-bold">'.$row["type"].'</span>';
                return $type;
            })
            ->addColumn('custom-status', function($row){
                $status = ($row['status']) ? 'Activated' : 'Deactivated';
                $custom_status = '<span class="cell-box  adsense-'.strtolower($status).'">'. $status .'</span>';
                return $custom_status;
            })
            ->rawColumns(['actions', 'custom-status', 'updated-on', 'custom-type'])
            ->make(true);
        }
        return view('panel.admin.adsense.index');
    }

    public function store(Request $request)
    {
        request()->validate([
            'code' => 'required',
        ]);
        
        $new_ad = new Ad();
        $new_ad->code =  request('code');
        $new_ad->status = request('status') == "on"? 1 : 0;
        $new_ad->save();

        return redirect()->route('dashboard.admin.ads.index')->with(['message' => __('Ad created successfully.'), 'type' => 'success']);
    }

    public function edit(Ad $id)
    {
        return view('panel.admin.adsense.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        request()->validate([
            'code' => 'required',
        ]);

        $ads = Ad::where('id', $id)->firstOrFail();
        $ads->code = request('code');
        $ads->status = request('status') == "on"? 1 : 0;
        $ads->save();    

        return redirect()->route('dashboard.admin.ads.index')->with(['message' => __('Ad updated successfully.'), 'type' => 'success']);
    }

    public function destroy($id)
    {
        $ads = Ad::where('id', $id)->firstOrFail();
        $ads->delete();
        return redirect()->route('dashboard.admin.ads.index')->with(['message' => __('Ad deleted successfully.'), 'type' => 'success']);
    }
}
