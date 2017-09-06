<?php

namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Admin\BaseController;
use App\Http\Requests\FAQ\CreateFAQRequest;
use App\Models\FAQ;
use Illuminate\Http\Request;

class FAQController extends BaseController
{
    private $_model;
    private $_view = 'content.faq';

    public function __construct(FAQ $model)
    {
        parent::__construct($model, $this->_view);
        $this->_model = $model;
    }

    public function postEdit(Request $request, $item_id = null )
    {
        $validator = \Validator::make($request->all(), with(new CreateFAQRequest())->rules());

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->all())->withErrors($validator->errors());
        }

        if( empty($item_id) || !is_numeric($item_id) || !($item = FAQ::find($item_id)) ) {
            $item = new FAQ();
        }

        $item->fill([
            'question'  => $request->get('question'),
            'answer'    => $request->get('answer'),
            'is_active' => $request->get('is_active') ? $request->get('is_active') : 0,
        ]);

        $item->save();

        return redirect()->route('admin.faq.get', ['id' => $item->id])->with('messages', ['Created successful']);
    }
}