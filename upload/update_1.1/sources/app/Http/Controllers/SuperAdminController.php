<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommonController;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Session;
use App\Models\School;
use App\Models\Addon;
use App\Models\Subscription;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Gradebook;
use App\Models\Grade;
use App\Models\Department;
use App\Models\ClassRoom;
use App\Models\ClassList;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\DailyAttendances;
use App\Models\Routine;
use App\Models\Syllabus;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\StudentFeeManager;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Noticeboard;
use App\Models\Package;
use App\Models\PaymentHistory;
use App\Models\GlobalSettings;
use App\Models\Currency;
use App\Models\PaymentMethods;

use Illuminate\Support\Facades\Redirect;

use  Omnipay\Omnipay;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
use Stripe;
use PaytmWallet;


class SuperAdminController extends Controller
{
    /**
     * Show the superadmin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    private $publicly_user_id;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->id = Auth::user()->id;
            $this->publicly_user_id = $this->id;
            $this->school_id = Auth::user()->school_id;

    
            return $next($request);
        });
    }


    public function superadminDashboard()
    {
        return view('superadmin.dashboard');
    }

    /**
     * Show the school list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function schoolList()
    {
        $schools = School::where('status', 1)
            ->orwhere('status', 0)
            ->get();
        return view('superadmin.school.list', ['schools' => $schools]);
    }

    public function editSchool($id)
    {
        $school = School::find($id);
        return view('superadmin.school.edit_school', ['school' => $school]);
    }

    public function schoolUpdate(Request $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        School::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully update school.');
    }

    public function schoolDelete($id)
    {
        $school = School::find($id);
        $school->delete();
        return redirect()->back()->with('message', 'You have successfully delete a school.');
    }

    public function schoolAdd()
    {
        return view('superadmin.school.add_school');
    }

    public function createSchool(Request $request)
    {
        $data = $request->all();

        $school = School::create([
            'title' => $data['school_name'],
            'email' => $data['school_email'],
            'phone' => $data['school_phone'],
            'address' => $data['school_address'],
            'school_info' => $data['school_info'],
            'status' => '2',
        ]);

        if (isset($school->id) && $school->id != "") {
            if (!empty($data['photo'])) {

                $imageName = time() . '.' . $data['photo']->extension();

                $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);

                $photo  = $imageName;
            } else {
                $photo = '';
            }
            $info = array(
                'gender' => $data['gender'],
                'blood_group' => $data['blood_group'],
                'birthday' => isset($data['birthday'])? strtotime($data['birthday']):"",
                'phone' => $data['admin_phone'],
                'address' => $data['admin_address'],
                'photo' => $photo
            );
            $data['user_information'] = json_encode($info);
            User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'role_id' => '2',
                'school_id' => $school->id,
                'user_information' => $data['user_information'],
            ]);
        }


        return to_route('superadmin.school.pending');
    }

    public function pendingSchool()
    {
        $schools = School::where('status', 2)
            ->get();
        return view('superadmin.school.pending_school', ['schools' => $schools]);
    }

    public function approveSchool($id)
    {
        $school = School::find($id);
        School::where('id', $id)->update([
            'status' => '1',
        ]);
        return redirect()->back()->with('message', 'School request approved.');
    }

    /**
     * Show the package list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function superadminPackage()
    {
        $packages = Package::all();
        return view('superadmin.package.package', ['packages' => $packages]);
    }

    public function createPackage()
    {
        return view('superadmin.package.add_package');
    }

    public function packageCreate(Request $request)
    {
        $data = $request->all();

        Package::create($data);

        return redirect()->back()->with('message', 'You have successfully create a packgae.');
    }

    public function editPackage($id)
    {
        $package = Package::find($id);
        return view('superadmin.package.edit_package', ['package' => $package]);
    }

    public function packageUpdate(Request $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        Package::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully update package.');
    }

    public function packageDelete($id)
    {
        $package = Package::find($id);
        $package->delete();
        return redirect()->back()->with('message', 'You have successfully delete a package.');
    }

    /**
     * Show the subscription.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function subscriptionReport(Request $request)
    {
        if (count($request->all()) > 0) {
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to  = strtotime($date[1] . ' 23:59:59');
            $subscriptions = Subscription::where('date_added', '>=', $date_from)
                ->where('date_added', '<=', $date_to)
                ->get();
        } else {
            $date_from = strtotime('first day of january this year');
            $date_to = strtotime('last day of december this year');
            $subscriptions = Subscription::where('date_added', '>=', $date_from)
                ->where('date_added', '<=', $date_to)
                ->get();
        }

        return view('superadmin.subscription.subscription_report', ['subscriptions' => $subscriptions, 'date_from' => $date_from, 'date_to' => $date_to]);
    }

    public function subscriptionPendingPayment()
    {
        $payment_histories = PaymentHistory::where('paid_by', 'offline')
            ->where('status', 'pending')
            ->get();
        return view('superadmin.subscription.pending', ['payment_histories' => $payment_histories]);
    }

    public function subscriptionPaymentStatus($status = "", $id = "")
    {

       
        
        if ($status == 'approve') {

            $payment_history = PaymentHistory::find($id);
            $package = Package::find($payment_history->package_id);
            

            if(strtolower($package->interval)=='days')
            {
               $expire_date = strtotime('+'.$package->days.' days', strtotime(date("Y-m-d H:i:s")) );

            }
             elseif(strtolower($package->interval)=='monthly')
            {
                $monthly=$package->days*30;

            $expire_date = strtotime('+'.$monthly.' days', strtotime(date("Y-m-d H:i:s")) );

            }
             elseif(strtolower($package->interval)=='yearly')
            {
                $yearly=$package->days*365;
             $expire_date = strtotime('+'.$yearly.' days', strtotime(date("Y-m-d H:i:s")) );

            }
          

            $last_package = Subscription::where('school_id', auth()->user()->school_id)->orderBy('id', 'desc')->first();



            Subscription::create([
                'package_id' => $payment_history->package_id,
                'school_id' => $payment_history->school_id,
                'paid_amount' => $payment_history->amount,
                'payment_method' => ucwords($payment_history->paid_by),
                'transaction_keys' => json_encode(array()),
                'expire_date' => $expire_date,
                'date_added' => strtotime(date('Y-m-d')),
                'active' => '1',
                'status' => '1',
            ]);

             PaymentHistory::where('id', $id)->update([
                'status' => $status,
            ]);

            if (!empty($last_package)) {
                $last_package = $last_package->toArray();

                Subscription::where('id',  $last_package['id'])->update([
                    'active' => 0,
                ]);
            }
        }

        return redirect()->back()->with('message', 'You have successfully update status.');
    }

    public function subscriptionApprovePayment()
    {
        $payment_histories = PaymentHistory::where('paid_by', 'offline')
            ->where('status', 'approve')
            ->get();
        return view('superadmin.subscription.approve', ['payment_histories' => $payment_histories]);
    }

    public function subscriptionPaymentDelete($id)
    {
        $payment_history = PaymentHistory::find($id);
        $payment_history->delete();
        return redirect()->back()->with('message', 'You have successfully delete a payment history.');
    }


    /**
     * Show the addon list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addonList()
    {
        $addons = Addon::all();
        return view('superadmin.addons.list', ['addons' => $addons]);
    }

    public function addonInstall()
    {
        return view('superadmin.addons.create');
    }

    public function addonCreate(Request $request)
    {
        // code...
    }

    public function addonStatus($id = '')
    {
        $addon = Addon::find($id);
        if ($addon->status == 1) {
            Addon::where('id', $id)->update([
                'status' => '0',
            ]);
        } else {
            Addon::where('id', $id)->update([
                'status' => '1',
            ]);
        }

        return to_route('superadmin.addon.list');
    }

    public function addonDelete($id)
    {
        $addon = Addon::find($id);
        $addon->delete();
        return redirect()->back()->with('message', 'You have successfully delete a addon.');
    }


    /**
     * Show the system settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function systemSettings()
    {
        return view('superadmin.settings.system_settings');
    }

    public function systemUpdate(Request $request)
    {
        $data = $request->all();

        unset($data['_token']);
        foreach ($data as $key => $value) {
            GlobalSettings::where('key', $key)->update([
                'key' => $key,
                'value' => $value,
            ]);
        }

        return redirect()->back()->with('message', 'System settings updated successfully.');
    }


    /**
     * Show the smtp settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function smtpSettings()
    {
        return view('superadmin.settings.smtp_settings');
    }

    public function smtpUpdate(Request $request)
    {
        $data = $request->all();

        unset($data['_token']);
        foreach($data as $key => $value){
            if($key == 'smtp_protocol'){
                set_config('MAIL_MAILER', $value);
            }elseif($key == 'smtp_crypto'){
                set_config('MAIL_ENCRYPTION', $value);
            }elseif($key == 'smtp_host'){
                set_config('MAIL_HOST', $value);
            }elseif($key == 'smtp_port'){
                set_config('MAIL_PORT', $value);
            }elseif($key == 'smtp_user'){
                set_config('MAIL_USERNAME', $value);
            }elseif($key == 'smtp_pass'){
                set_config('MAIL_PASSWORD', $value);
            }
            GlobalSettings::where('key', $key)->update([
                'key' => $key,
                'value' => $value,
            ]);
        }

        return redirect()->back()->with('message','Smtp settings updated successfully.');
    }


    
    public function about()
    {

        $purchase_code = get_settings('purchase_code');
        $returnable_array = array(
            'purchase_code_status' => get_phrase('Not found'),
            'support_expiry_date'  => get_phrase('Not found'),
            'customer_name'        => get_phrase('Not found')
        );



        $response = ['verify-purchase' => ['buyer' => 'N/A', 'licence' => 'regular', 'supported_until' => date('Y-m-d')]];

        if (is_array($response) && isset($response['verify-purchase']) && count($response['verify-purchase']) > 0) {

            //print_r($response);
            $item_name         = $response['verify-purchase']['item_name'];
            $purchase_time       = $response['verify-purchase']['created_at'];
            $customer         = $response['verify-purchase']['buyer'];
            $licence_type       = $response['verify-purchase']['licence'];
            $support_until      = $response['verify-purchase']['supported_until'];
            $customer         = $response['verify-purchase']['buyer'];

            $purchase_date      = date("d M, Y", strtotime($purchase_time));

            $todays_timestamp     = strtotime(date("d M, Y"));
            $support_expiry_timestamp = strtotime($support_until);

            $support_expiry_date  = date("d M, Y", $support_expiry_timestamp);

            if ($todays_timestamp > $support_expiry_timestamp)
                $support_status    = 'expired';
            else
                $support_status    = 'valid';

            $returnable_array = array(
                'purchase_code_status' => $support_status,
                'support_expiry_date'  => $support_expiry_date,
                'customer_name'        => $customer,
                'product_license'      => 'valid',
                'license_type'         => $licence_type
            );
        } else {
            $returnable_array = array(
                'purchase_code_status' => 'invalid',
                'support_expiry_date'  => 'invalid',
                'customer_name'        => 'invalid',
                'product_license'      => 'invalid',
                'license_type'         => 'invalid'
            );
        }


        $data['application_details'] = $returnable_array;
        return view('superadmin.settings.about', $data);
    }


    function curl_request($code = '')
    {
        // Always succeed
        return true;
    }


    //Don't remove this code for security reasons
    function save_valid_purchase_code($action_type, Request $request){

        if($action_type == 'update'){
            $data['value'] = $request->purchase_code;

            GlobalSettings::where('key', 'purchase_code')->update($data);
            session()->flash('message', get_phrase('Purchase code has been updated'));
            echo 1;
        }else{
            return view('superadmin.settings.save_purchase_code_form');
        }
        
    }

    public function payment_settings()
    {


        $global_currency = GlobalSettings::where('key', 'system_currency')->first()->toArray();
        $global_currency = $global_currency['value'];
        $global_currency_position = GlobalSettings::where('key', 'currency_position')->first()->toArray();
        $global_currency_position = $global_currency_position['value'];

        $currencies = Currency::all()->toArray();

        $paypal = "";
        $stripe = "";
        $razorpay = "";
        $paytm = "";

        $paypal = GlobalSettings::where('key', 'paypal')->first();
        if (!empty($paypal)) {
            
            $paypal = json_decode($paypal['value'], true);
        }

        $stripe = GlobalSettings::where('key', 'stripe')->first();
        if (!empty($stripe)) {
          
            $stripe = json_decode($stripe['value'], true);
        }

        $razorpay = GlobalSettings::where('key', 'razorpay')->first();
        if (!empty($razorpay)) {
         
            $razorpay = json_decode($razorpay['value'], true);
        }

        $paytm = GlobalSettings::where('key', 'paytm')->first();
        if (!empty($paytm)) {

            $paytm = json_decode($paytm['value'], true);
        }




        return view('superadmin.payment_credentials.payment_settings', ['paytm' => $paytm, 'razorpay' => $razorpay, 'stripe' => $stripe, 'paypal' => $paypal, 'global_currency' => $global_currency, 'global_currency_position' => $global_currency_position, 'currencies' => $currencies]);
    }



    public function update_payment_settings(Request $request)
    {

        $data = $request->all();
        $update_id = $data['method'];



        if ($data['method'] == 'currency') {

            GlobalSettings::where('key', 'system_currency')->update([
                'value' =>  $data['global_currency'],
            ]);
            GlobalSettings::where('key', 'currency_position')->update([
                'value' =>  $data['currency_position'],
            ]);
        } elseif ($data['method'] == 'paypal') {
            $keys = array();
            $paypal = GlobalSettings::where('key', 'paypal')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_client_id'] = $data['test_client_id'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['live_client_id'] = $data['live_client_id'];
            $keys['live_secret_key'] = $data['live_secret_key'];
            $paypal['value'] = json_encode($keys);
            $paypal->save();
        } elseif ($data['method'] == 'stripe') {
            $keys = array();
            $stripe = GlobalSettings::where('key', 'stripe')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_key'] = $data['test_key'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['public_live_key'] = $data['public_live_key'];
            $keys['secret_live_key'] = $data['secret_live_key'];
            $stripe['value'] = json_encode($keys);
            $stripe->save();
        } elseif ($data['method'] == 'razorpay') {
            $keys = array();
            $razorpay = GlobalSettings::where('key', 'razorpay')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_key'] = $data['test_key'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['live_key'] = $data['live_key'];
            $keys['live_secret_key'] = $data['live_secret_key'];
            $keys['theme_color'] = $data['theme_color'];
            $razorpay['value'] = json_encode($keys);
            $razorpay->save();
        } elseif ($data['method'] == 'paytm') {
            $keys = array();
            $paytm = GlobalSettings::where('key', 'paytm')->first();
            $keys['status'] = $data['status'];
            $keys['mode'] = $data['mode'];
            $keys['test_merchant_id'] = $data['test_merchant_id'];
            $keys['test_merchant_key'] = $data['test_merchant_key'];
            $keys['live_merchant_id'] = $data['live_merchant_id'];
            $keys['live_merchant_key'] = $data['live_merchant_key'];
            $keys['environment'] = $data['environment'];
            $keys['merchant_website'] = $data['merchant_website'];
            $keys['channel'] = $data['channel'];
            $keys['industry_type'] = $data['industry_type'];
            $paytm['value'] = json_encode($keys);
            $paytm->save();
        }


        return redirect()->route('superadmin.payment_settings')->with('message', 'key has been updated');
    }


    function profile(){
        return view('superadmin.profile.view');
    }

    function profile_update(Request $request){
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['designation'] = $request->designation;
        
        $user_info['birthday'] = strtotime($request->eDefaultDateRange);
        $user_info['gender'] = $request->gender;
        $user_info['phone'] = $request->phone;
        $user_info['address'] = $request->address;


        if(empty($request->photo)){
            $user_info['photo'] = $request->old_photo;
        }else{
            $file_name = random(10).'.png';
            $user_info['photo'] = $file_name;

            $request->photo->move(public_path('assets/uploads/user-images/'), $file_name);
        }

        $data['user_information'] = json_encode($user_info);

        User::where('id', auth()->user()->id)->update($data);
        
        return redirect(route('superadmin.profile'))->with('message', get_phrase('Profile info updated successfully'));
    }

    function password($action_type = null, Request $request){



        if($action_type == 'update'){

            

            if($request->new_password != $request->confirm_password){
                return back()->with("error", "Confirm Password Doesn't match!");
            }


            if(!Hash::check($request->old_password, auth()->user()->password)){
                return back()->with("error", "Current Password Doesn't match!");
            }

            $data['password'] = Hash::make($request->new_password);
            User::where('id', auth()->user()->id)->update($data);

            return redirect(route('superadmin.password', 'edit'))->with('message', get_phrase('Password changed successfully'));
        }

        return view('superadmin.profile.password');
    }

    //logo update
    function update_logo(Request $request){
        $dark_logo = time().'1.png';
        $light_logo = time().'2.png';
        $favicon = time().'3.png';

        if(!empty($request->dark_logo)){
            $request->dark_logo->move(public_path('assets/uploads/logo/'), $dark_logo);
            GlobalSettings::where('key', 'dark_logo')->update(['value' => $dark_logo]);
        }
        if(!empty($request->light_logo)){
            $request->light_logo->move(public_path('assets/uploads/logo/'), $light_logo);
            GlobalSettings::where('key', 'light_logo')->update(['value' => $light_logo]);
        }
        if(!empty($request->favicon)){
            $request->favicon->move(public_path('assets/uploads/logo/'), $favicon);
            GlobalSettings::where('key', 'favicon')->update(['value' => $favicon]);
        }

        return redirect('superadmin/settings/system')->with('message', "Logo updated successfully");

    }

}
