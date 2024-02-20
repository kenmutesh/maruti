<?php

namespace App\Http\Controllers;

use App\Models\DocumentLabel;
use Illuminate\Http\Request;

class DocumentLabelController extends Controller
{

    public function index(Request $request)
    { 
      $documentlabels = DocumentLabel::all();
      $this->authorize('viewAny', DocumentLabel::class);
      if($request->is('api/*')){
        return $documentlabels;
      }else{
        return view('system.documentlabel.index',[
          'documentLabels' => $documentlabels,
        ]);
      }
    }

    public function update(Request $request, DocumentLabel $documentlabel)
    {
      $this->authorize('update', DocumentLabel::class);
      $request->validate([
          'id' => ['required'],
          'document_suffix' => ['required'],
      ]);

      $documentlabel->document_prefix = strtoupper($request->document_prefix);

      $documentlabel->document_suffix = strtoupper($request->document_suffix);

      if($documentlabel->update()){
        if($request->is('api/*')){
          return $documentlabel;
        }else{
            return back()->with('Success', 'Label has been edited successfully');
        }
      }else {
        return back()->with('Error', 'Failed to edit label. Please retry');
      }
    }
}
