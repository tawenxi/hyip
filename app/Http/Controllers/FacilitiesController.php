<?php

namespace App\Http\Controllers;

use App\Http\Requests\Facilities\RefillRequest;
use App\Models\PaymentsRequest;
use App\Models\Settings;
use App\Models\User;
use App\Models\WalletProcesses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\SocialNetwork;
use App\Models\InputOutput;
use App\Helpers\CPayeer;
use Illuminate\Support\Facades\Validator;

class FacilitiesController extends Controller
{
    public function index($type)
    {
        $social = SocialNetwork::where(['is_active' => 1])->get();
        $item = InputOutput::where(['need_show' => 1, 'lang' => Session::get("applocale")])->first();
        $settings = Settings::find(1);

        $data = [
            'contacts' => [
                'social' => [
                    'links' => $social
                ]
            ]
        ];

        return view('cabinet.facilities.index', [
            'data' => $data,
            'type' => $type,
            'item' => $item,
            'pay_systems' => config('payment.pay_systems'),
            'settings' => $settings
        ]);
    }

    public function operations()
    {
        $social = SocialNetwork::where(['is_active' => 1])->get();
        $operations = WalletProcesses::with('getType')->where(['to_id' => \Auth::user()->id])->orderBy('time', 'desc')->paginate(15);
        $data = [
            'contacts' => [
                'social' => [
                    'links' => $social
                ]
            ]
        ];
        return view('cabinet.facilities.operations', [
            'data' => $data,
            'operations' => $operations,
        ]);
    }

    public function refill(RefillRequest $request)
    {
        $user_id = \Auth::id();

        $payment = WalletProcesses::create([
            'type_id' => WalletProcesses::INPUT,
            'from_id' => $user_id,
            'to_id' => $user_id,
            'value' => $request->get('count'),
            'status' => 0,
            'time' => Carbon::now()
        ]);

        $settings = Settings::find(1);

        /*PaymentsRequest::create([
            'user_id' => $user_id,
            'summ' => $request->get('count'),
            'status' => 0,
            'comment' => 'refill',
        ]);*/

        $payment_config = config('payment');

        $m_shop = $settings->payeer_m_shop;
        $m_orderid = $payment->id;
        $m_amount = number_format($request->get('count'), 2, '.', '');
        $m_curr = $payment_config['m_curr'];
        $m_desc = base64_encode('Пополнение баланса');
        $m_key = $settings->payeer_m_key;

        $arHash = [
            $m_shop,
            $m_orderid,
            $m_amount,
            $m_curr,
            $m_desc,
            $m_key,
        ];

        $sign = strtoupper(hash('sha256', implode(':', $arHash)));

        return redirect("https://payeer.com/merchant/?m_shop=$m_shop&m_orderid=$m_orderid&m_amount=$m_amount&m_curr=$m_curr&m_desc=$m_desc&m_sign=$sign");
    }

    public function statusResult(Request $request)
    {

        if (!in_array($_SERVER['REMOTE_ADDR'], ['185.71.65.92', '185.71.65.189', '149.202.17.210'])) return;
        $settings = Settings::find(1);
        if (isset($_POST['m_operation_id']) && isset($_POST['m_sign'])) {
            $m_key = $settings->payeer_m_key;
            $arHash = array(
                $_POST['m_operation_id'],
                $_POST['m_operation_ps'],
                $_POST['m_operation_date'],
                $_POST['m_operation_pay_date'],
                $_POST['m_shop'],
                $_POST['m_orderid'],
                $_POST['m_amount'],
                $_POST['m_curr'],
                $_POST['m_desc'],
                $_POST['m_status']
            );

            $arHash[] = $m_key;
            $sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));

            if ($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success') {
                $payment = WalletProcesses::find($_POST['m_orderid']);

                if (!isset($payment) || $payment->status == 1) {
                    exit($_POST['m_orderid'] . '|error');
                }

                $payment->update([
                    'status' => 1
                ]);

                $user = User::find($payment->user_id);
                $user->balance += $_POST['m_amount'];
                $user->save();

                exit($_POST['m_orderid'] . '|success');
            }
            exit($_POST['m_orderid'] . '|error');
        }
    }

    public function getResultRefill(Request $request, $type)
    {
        if ($type == "success") {
            Session::flash('messages', [__("messages.created_successful")]);
            return redirect()->route('facilities', ['type' => 'input']);
        }
        return redirect()->route('facilities', ['type' => 'input'])->withErrors(__("messages.payment_error"));
    }

    public function withdraw(Request $request)
    {
        $user = \Auth::user();
        $settings = Settings::find(1);
        $payeer = new CPayeer($settings->payeer_number, $settings->payeer_api_id, $settings->payeer_api_key);
        if ($payeer->isAuth()) {
            $balance = $payeer->getBalance()['balance'];
        } else {
            $balance = [];
        }

        if (count($balance) == 0) {
            return redirect()->route('facilities', ['type' => 'output'])->withErrors([__("messages.system_settings_error")]);
        }

        $validator = Validator::make($request->all(), [
            'sum' => 'required|numeric|min:' . $settings->min_sum . '|max:' . $settings->max_sum,
            'pay_system' => 'required',
            'card_number' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('facilities', ['type' => 'output'])->withErrors($validator);
        }

        $sum = $request->get('sum');
        $pay_system = $request->get('pay_system');
        $card_number = $request->get('card_number');
        $contact_person = $request->has('contact_person') ? $request->get('contact_person') : null;


        if($user->balance < $sum){
            return redirect()->route('facilities', ['type' => 'output'])->withErrors([__("messages.no_balance")]);
        }

        if ($balance['RUB']['DOSTUPNO'] < $sum) {
            return redirect()->route('facilities', ['type' => 'output'])->withErrors([__("messages.system_settings_error")]);
        }

        $payment = WalletProcesses::create([
            'type_id' => WalletProcesses::OUTPUT,
            'to_id' => $user->id,
            'from_id' => $user->id,
            'value' => $sum,
            'status' => 0,
            'card_number' => $card_number,
            'pay_system' => $pay_system,
            'contact_person' => $contact_person,
            'time' => Carbon::now()
        ]);

        $user->balance = $user->balance - $sum;
        $user->save();

        Session::flash('messages', [__("messages.request_output_successfully")]);
        return redirect()->route('facilities', ['type' => 'output']);

    }

    public function getPaySystem($id)
    {
        $pay_system = config('payment.pay_systems')[$id];

        if (count($pay_system['r_fields']) > 1) {
            return json_encode(["success" => true]);
        } else {

            return json_encode(["success" => false]);
        }

    }
}
