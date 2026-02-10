<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CommonController;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Session;
use App\Models\School;
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
use App\Models\ClassFeeStructure;
use App\Models\FeeConcession;
use App\Models\FeeSiblingDiscount;
use App\Models\SchoolApplication;
use App\Mail\ApplicationStatusEmail;
use Illuminate\Support\Facades\Log;
use App\Models\Book;
use App\Models\Chat;
use App\Models\MessageThrade;
use App\Models\BookIssue;
use App\Models\Noticeboard;
use App\Models\FrontendEvent;
use App\Models\Package;
use App\Models\PaymentMethods;
use App\Models\Currency;
use App\Models\PaymentHistory;
use App\Models\TeacherPermission;
use App\Models\Payments;
use App\Models\Feedback;
use App\Models\Appraisal;
use App\Models\Appraisal_submit;
use App\Models\Guardian;
use App\Models\StudentGuardian;
use App\Models\StudentWithdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\FreeEmail;
use App\Mail\StudentsEmail;
use App\Mail\NewUserEmail;
use App\Events\MessageSent;

use PDF;


use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    private $user;
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth()->user();
            $this->check_subscription_status(Auth()->user()->school_id);
            $this->insert_gateways();
            return $next($request);
        });
    }

    function check_subscription_status($school_id = "")
    {
        $current_route = Route::currentRouteName();
        $has_subscription = Subscription::where('school_id', $school_id)->where('status', 1)->get()->count();
        $active_subscription = Subscription::where('school_id', $school_id)->where('active', 1)->first();

        $today = date("Y-m-d");
        $today_time = strtotime($today);

        if ($has_subscription != 0) {
            if ($active_subscription['expire_date'] == '0') {
                $expiry_status = '0';
            } else {
                $expiry_status = (int)$active_subscription['expire_date'] < $today_time;
            }

            if (
                ($current_route != 'admin.subscription' && $expiry_status) &&
                ($current_route != 'admin.subscription.purchase' && $expiry_status) &&
                ($current_route != 'admin.subscription.payment' && $expiry_status) &&
                ($current_route != 'admin.subscription.offline_payment' && $expiry_status)
            ) {
                redirect()->route('admin.subscription')->send();
            }
        } else {

            if (
                ($current_route != 'admin.subscription' && $has_subscription == 0) &&
                ($current_route != 'admin.subscription.purchase' && $has_subscription == 0) &&
                ($current_route != 'admin.subscription.payment' && $has_subscription == 0) &&
                ($current_route != 'admin.subscription.offline_payment' && $has_subscription == 0)
            ) {
                redirect()->route('admin.subscription')->send();
            }
        }
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function check_admin_subscription($school_id)
    {
        $validity_of_current_package = Subscription::where('school_id', $school_id)->where('active', 1)->first();
        if (!empty($validity_of_current_package)) {
            $validity_of_current_package = $validity_of_current_package->toArray();


            $today = date("Y-m-d");
            $today_time = strtotime($today);

            if ((int)$validity_of_current_package['expire_date'] < $today_time) {
                $this->adminDashboard();
            } else {
            }
        } else {
        }
    }



    public function adminDashboard()
    {
        $account_status = auth()->user()->account_status;
        if (auth()->user()->role_id != "") {
            return view('admin.dashboard');
        } else {
            redirect()->route('login')
                ->with('error', 'You are not logged in.');
        }
    }

    /**
     * Show the admin list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function adminList(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {

            $admins = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 2);
            })->orWhere(function ($query) use ($search) {
                $query->where('email', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 2);
            })->paginate(10);
        } else {
            $admins = User::where('role_id', 2)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.admin.admin_list', compact('admins', 'search'));
    }

    /**
     * Show the admin add modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createModal()
    {
        return view('admin.admin.add_admin');
    }

    public function adminCreate(Request $request)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');

            $imageName = time() . '.' . $data['photo']->extension();

            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $photo = '';
        }

        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo,
            'school_role' => 0,
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);


        if (count($duplicate_user_check) == 0) {

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => '2',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
            ]);
        } else {
            return redirect()->back()->with('error', 'Email was already taken.');
        }
        if (!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))) {
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }
        return redirect()->back()->with('message', 'You have successfully add user.');
    }

    public function editModal($id)
    {
        $user = User::find($id);
        return view('admin.admin.edit_admin', ['user' => $user]);
    }

    public function adminUpdate(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
            $user_information = User::where('id', $id)->value('user_information');
            $old_photo = $user_information ? (json_decode($user_information)->photo ?? '') : '';

            $imageName = time() . '.' . $data['photo']->extension();

            delete_upload_file('assets/uploads/user-images', $old_photo);
            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if ($file_name != '') {
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);

        return redirect()->back()->with('message', 'You have successfully update user.');
    }

    public function adminDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 2);
        return redirect()->route('admin.admin')->with('message', 'You have successfully deleted user.');
    }

    public function menuSettingsView($id)
    {
        $user = User::find($id);
        return view('admin.admin.menu_permission', ['user' => $user]);
    }


    public function menuPermissionUpdate(Request $request, $id)
    {

        User::where('id', $id)->update([
            'menu_permission' => json_encode($request->permissions),
        ]);

        return redirect()->back()->with('message', 'You have successfully updated user permissions.');
    }


    public function adminProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.admin.admin_profile', ['user_details' => $user_details]);
        // return view('admin.admin.admin_profile');
    }

    function school_user_password(Request $request)
    {

        $userId = $request->input('user_id');

        $data['password'] = Hash::make($request->password);
        User::where('id', $userId)->update($data);

        return redirect()->back()->with('message', 'You have successfully update password.');
    }

    public function adminDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.admin.documents', ['user_details' => $user_details]);
    }

    public function accountantDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.accountant.documents', ['user_details' => $user_details]);
    }

    public function librarianDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.librarian.documents', ['user_details' => $user_details]);
    }

    public function parentDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.parent.documents', ['user_details' => $user_details]);
    }

    public function studentDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.student.documents', ['user_details' => $user_details]);
    }

    public function teacherDocuments($id = "")
    {
        $user_details = User::find($id);
        return view('admin.teacher.documents', ['user_details' => $user_details]);
    }

    public function documentsUpload(Request $request, $id = "")
    {
        // Validate the request
        $request->validate([
            'file_name' => 'required',
            'file' => 'required',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        // Get the current user
        $user = User::find($id);

                $filePath = $file->storeAs('assets/uploads/user-docs/' . $user->id, $fileName, 'public');

        // Get existing documents or initialize as an empty array
        $documents = $user->documents ? json_decode($user->documents, true) : [];

        // Add the new document with the provided file name
        $documents[slugify($request->input('file_name'))] = $fileName;

        // Update the user's documents
        $user->update(['documents' => json_encode($documents)]);

        return redirect()->back()->with('message', 'File uploaded successfully.');
    }

    public function documentsRemove($id = "", $file_name = "")
    {
        // Find the user by ID
        $user = User::find($id);

        if ($user) {
            // Get the documents as an array
            $documents = json_decode($user->documents, true);

            // Check if the file with the given file_name exists
            if (isset($documents[$file_name])) {
                $file_path = storage_path('app/public/assets/uploads/user-docs/' . $user->id . '/' . $documents[$file_name]);

                // Check if the file exists
                if (file_exists($file_path)) {
                    // Delete the file
                    unlink($file_path);

                    // Remove the file entry from the documents array
                    unset($documents[$file_name]);

                    // Update the user's documents column
                    $user->update(['documents' => json_encode($documents)]);

                    return redirect()->back()->with('message', 'File removed successfully.');
                } else {
                    return redirect()->back()->with('error', 'File not found.');
                }
            } else {
                return redirect()->back()->with('error', 'File not found.');
            }
        } else {
            return redirect()->back()->with('error', 'User not found.');
        }
    }


    /**
     * Show the teacher list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function teacherList(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {

            $teachers = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 3);
            })->orWhere(function ($query) use ($search) {
                $query->where('email', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 3);
            })->paginate(10);
        } else {
            $teachers = User::where('role_id', 3)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.teacher.teacher_list', compact('teachers', 'search'));
    }

    /**
     * Show the teacher add modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function createTeacherModal()
    {
        $departments = Department::get()->where('school_id', auth()->user()->school_id);
        return view('admin.teacher.add_teacher', ['departments' => $departments]);
    }

    public function adminTeacherCreate(Request $request)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');

            $imageName = time() . '.' . $data['photo']->extension();

            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if (count($duplicate_user_check) == 0) {

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => '3',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
                'department_id' => $data['department_id'],
                'designation' => $data['designation'],
            ]);
        } else {
            return redirect()->back()->with('error', 'Email was already taken.');
        }
        if (!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))) {
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }
        return redirect()->back()->with('message', 'You have successfully add teacher.');
    }

    public function teacherEditModal($id)
    {
        $user = User::find($id);
        $departments = Department::get()->where('school_id', auth()->user()->school_id);
        return view('admin.teacher.edit_teacher', ['user' => $user, 'departments' => $departments]);
    }

    public function teacherUpdate(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
            $user_information = User::where('id', $id)->value('user_information');
            $old_photo = $user_information ? (json_decode($user_information)->photo ?? '') : '';

            $imageName = time() . '.' . $data['photo']->extension();

            delete_upload_file('assets/uploads/user-images', $old_photo);
            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if ($file_name != '') {
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
            'department_id' => $data['department_id'],
            'designation' => $data['designation'],
        ]);
        return redirect()->back()->with('message', 'You have successfully update teacher.');
    }

    public function teacherDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 3);
        return redirect()->route('admin.teacher')->with('message', 'You have successfully deleted teacher.');
    }
    public function teacherProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.teacher.teacher_profile', ['user_details' => $user_details]);
    }

    /**
     * Show the accountant list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function accountantList(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {

            $accountants = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 4);
            })->orWhere(function ($query) use ($search) {
                $query->where('email', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 4);
            })->paginate(10);
        } else {
            $accountants = User::where('role_id', 4)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.accountant.accountant_list', compact('accountants', 'search'));
    }

    public function createAccountantModal()
    {
        return view('admin.accountant.add_accountant');
    }

    public function accountantCreate(Request $request)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');

            $imageName = time() . '.' . $data['photo']->extension();

            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );
        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if (count($duplicate_user_check) == 0) {

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => '4',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
            ]);
        } else {
            return redirect()->back()->with('error', 'Email was already taken.');
        }
        if (!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))) {
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }
        return redirect()->back()->with('message', 'You have successfully add accountant.');
    }

    public function accountantEditModal($id)
    {
        $user = User::find($id);
        return view('admin.accountant.edit_accountant', ['user' => $user]);
    }

    public function accountantUpdate(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
            $user_information = User::where('id', $id)->value('user_information');
            $old_photo = $user_information ? (json_decode($user_information)->photo ?? '') : '';

            $imageName = time() . '.' . $data['photo']->extension();

            delete_upload_file('assets/uploads/user-images', $old_photo);
            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if ($file_name != '') {
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);

        return redirect()->back()->with('message', 'You have successfully update accountant.');
    }

    public function accountantDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 4);
        return redirect()->route('admin.accountant')->with('message', 'You have successfully deleted accountant.');
    }

    public function accountantProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.accountant.accountant_profile', ['user_details' => $user_details]);
    }

    /**
     * Show the librarian list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function librarianList(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {

            $librarians = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 5);
            })->orWhere(function ($query) use ($search) {
                $query->where('email', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 5);
            })->paginate(10);
        } else {
            $librarians = User::where('role_id', 5)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.librarian.librarian_list', compact('librarians', 'search'));
    }

    public function createLibrarianModal()
    {
        return view('admin.librarian.add_librarian');
    }

    public function librarianCreate(Request $request)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');

            $imageName = time() . '.' . $data['photo']->extension();

            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if (count($duplicate_user_check) == 0) {

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => '5',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
            ]);
        } else {
            return redirect()->back()->with('error', 'Email was already taken.');
        }
        if (!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))) {
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }
        return redirect()->back()->with('message', 'You have successfully add librarian.');
    }

    public function librarianEditModal($id)
    {
        $user = User::find($id);
        return view('admin.librarian.edit_librarian', ['user' => $user]);
    }

    public function librarianUpdate(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
            $user_information = User::where('id', $id)->value('user_information');
            $old_photo = $user_information ? (json_decode($user_information)->photo ?? '') : '';

            $imageName = time() . '.' . $data['photo']->extension();

            delete_upload_file('assets/uploads/user-images', $old_photo);
            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if ($file_name != '') {
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);
        return redirect()->back()->with('message', 'You have successfully update librarian.');
    }

    public function librarianDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 5);
        return redirect()->route('admin.librarian')->with('message', 'You have successfully deleted librarian.');
    }

    public function librarianProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.librarian.librarian_profile', ['user_details' => $user_details]);
    }


    /**
     * Show the parent list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function parentList(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {

            $parents = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 6);
            })->orWhere(function ($query) use ($search) {
                $query->where('email', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 6);
            })->paginate(10);
        } else {
            $parents = User::where('role_id', 6)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.parent.parent_list', compact('parents', 'search'));
    }

    public function createParent()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.parent.add_parent', ['classes' => $classes]);
    }


    public function parentCreate(Request $request)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();

        if (!empty($data['id_card_no']) && !is_valid_id_card_no($data['id_card_no'])) {
            return redirect()->back()->with('error', 'Parent ID card number must be exactly 13 digits.');
        }
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');

            $imageName = time() . '.' . $data['photo']->extension();

            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo,
            'id_card_no' => $data['id_card_no'] ?? null
        );

        $data['user_information'] = json_encode($info);

        $duplicate_user_check = User::get()->where('email', $data['email']);

        if (count($duplicate_user_check) == 0) {

            $parent = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => '6',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
            ]);
        } else {
            return redirect()->back()->with('error', 'Email was already taken.');
        }

        // Ensure guardian record exists for this parent CNIC
        $school_id = (int) auth()->user()->school_id;
        $cnic = (string) ($data['id_card_no'] ?? '');
        $normalized = normalize_id_card_no($cnic);
        if ($normalized !== '') {
            $guardian = Guardian::where('school_id', $school_id)
                ->where('id_card_no_normalized', $normalized)
                ->first();
            if (empty($guardian)) {
                $guardian = Guardian::create([
                    'school_id' => $school_id,
                    'user_id' => $parent->id,
                    'name' => $parent->name,
                    'id_card_no' => $cnic,
                    'id_card_no_normalized' => $normalized,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);
            } else {
                Guardian::where('id', $guardian->id)->update([
                    'user_id' => $guardian->user_id ?: $parent->id,
                    'name' => $guardian->name ?: $parent->name,
                    'id_card_no' => $guardian->id_card_no ?: $cnic,
                    'phone' => $guardian->phone ?: ($data['phone'] ?? null),
                    'address' => $guardian->address ?: ($data['address'] ?? null),
                ]);
            }
        }
        $students = $data['student_id'];
        $class_id = $data['class_id'];
        $section_id = $data['student_id'];

        foreach ($students as $student) {
            $users = User::where('id', $student)->get();

            if (count($users) == 1) {
                    User::where('id', $student)->update([
                        'parent_id' => $parent->id,
                    ]);

                    // Link student to guardian (if we have CNIC)
                    if (isset($guardian) && !empty($guardian)) {
                        StudentGuardian::firstOrCreate(
                            ['student_id' => (int) $student, 'guardian_id' => (int) $guardian->id, 'relation' => 'father'],
                            ['is_primary' => 1, 'is_fee_payer' => 1]
                        );
                    }
            } else {
                if (count($users) > 1) {
                    foreach ($users as $user) {
                        $data = Enrollment::where('class_id', $class_id)->where('section_id', $section_id)->where('user_id', $user->id)->where('school_id', auth()->user()->school_id)->first();

                        if ($data != '') {
                            User::where('id', $user->id)->update([
                                'parent_id' => $parent->id,
                            ]);
                        }
                    }
                }
            }
        }
        if (!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))) {
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }

        return redirect()->back()->with('message', 'You have successfully add parent.');
    }


    public function parentEditModal($id)
    {
        $user = User::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.parent.edit_parent', ['user' => $user, 'classes' => $classes]);
    }

    public function parentUpdate(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();

        if (!empty($data['id_card_no']) && !is_valid_id_card_no($data['id_card_no'])) {
            return redirect()->back()->with('error', 'Parent ID card number must be exactly 13 digits.');
        }


        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
            $user_information = User::where('id', $id)->value('user_information');
            $old_photo = $user_information ? (json_decode($user_information)->photo ?? '') : '';

            $imageName = time() . '.' . $data['photo']->extension();

            delete_upload_file('assets/uploads/user-images', $old_photo);
            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {

            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if ($file_name != '') {
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }

        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo,
            'id_card_no' => $data['id_card_no'] ?? null
        );

        $data['user_information'] = json_encode($info);

        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);

        // Sync guardian CNIC for this parent user
        $school_id = (int) auth()->user()->school_id;
        $cnic = (string) ($data['id_card_no'] ?? '');
        $normalized = normalize_id_card_no($cnic);
        if ($normalized !== '') {
            $guardian = Guardian::where('school_id', $school_id)
                ->where('id_card_no_normalized', $normalized)
                ->first();
            if (empty($guardian)) {
                Guardian::create([
                    'school_id' => $school_id,
                    'user_id' => (int) $id,
                    'name' => $data['name'] ?? null,
                    'id_card_no' => $cnic,
                    'id_card_no_normalized' => $normalized,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);
            } else {
                Guardian::where('id', $guardian->id)->update([
                    'user_id' => $guardian->user_id ?: (int) $id,
                    'name' => $guardian->name ?: ($data['name'] ?? null),
                    'id_card_no' => $guardian->id_card_no ?: $cnic,
                    'phone' => $guardian->phone ?: ($data['phone'] ?? null),
                    'address' => $guardian->address ?: ($data['address'] ?? null),
                ]);
            }
        }


        //Previous parent has been empty
        User::where('parent_id', $id)->update(['parent_id' => null]);


        $students = $data['student_id'];
        foreach ($students as $student) {
            if ($student != '') {
                $user = User::where('id', $student)->first();

                if ($user != '') {
                    User::where('id', $user->id)->update([
                        'parent_id' => $id,
                    ]);
                }
            }
        }

        return redirect()->back()->with('message', 'You have successfully update parent.');
    }

    public function parentDelete($id)
    {
        $user = User::find($id);
        $user->delete();
        $admins = User::get()->where('role_id', 5);
        return redirect()->route('admin.parent')->with('message', 'You have successfully deleted parent.');
    }


    public function parentProfile($id)
    {
        $user_details = (new CommonController)->getAdminDetails($id);
        return view('admin.parent.parent_profile', ['user_details' => $user_details]);
    }
    /**
     * Show the student list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function studentList(Request $request)
    {
        $search = (string) ($request->input('search') ?? '');
        $class_id = (string) ($request->input('class_id') ?? '');
        $section_id = (string) ($request->input('section_id') ?? '');

        $school_id = (int) auth()->user()->school_id;

        $query = DB::table('enrollments as e')
            ->join('users as s', 's.id', '=', 'e.user_id')
            ->leftJoin('classes as c', 'c.id', '=', 'e.class_id')
            ->leftJoin('sections as sec', 'sec.id', '=', 'e.section_id')
            ->leftJoin('student_withdrawals as sw', function ($join) use ($school_id) {
                $join->on('sw.student_id', '=', 's.id')
                    ->where('sw.school_id', '=', $school_id);
            })
            ->leftJoin('student_guardians as sg', function ($join) {
                $join->on('sg.student_id', '=', 's.id')
                    ->where('sg.relation', '=', 'father')
                    ->where('sg.is_primary', '=', 1);
            })
            ->leftJoin('guardians as g', 'g.id', '=', 'sg.guardian_id')
            ->leftJoin('users as p', 'p.id', '=', 's.parent_id')
            ->where('s.school_id', $school_id)
            ->where('s.role_id', 7);

        if ($class_id !== '' && $class_id !== 'all') {
            $query->where('e.class_id', (int) $class_id);
        }

        if ($section_id !== '' && $section_id !== 'all') {
            $query->where('e.section_id', (int) $section_id);
        }

        if ($search !== '') {
            $searchDigits = normalize_id_card_no($search);
            $query->where(function ($q) use ($search, $searchDigits) {
                $q->where('s.name', 'LIKE', "%{$search}%")
                    ->orWhere('s.email', 'LIKE', "%{$search}%")
                    ->orWhere('g.name', 'LIKE', "%{$search}%")
                    ->orWhere('g.id_card_no', 'LIKE', "%{$search}%");
                if ($searchDigits !== '') {
                    $q->orWhere('g.id_card_no_normalized', 'LIKE', "%{$searchDigits}%");
                }
            });
        }

        $students = $query->select([
            'e.*',
            's.name as student_name',
            's.email as student_email',
            's.user_information as student_user_information',
            's.account_status as student_account_status',
            'c.name as class_name',
            'sec.name as section_name',
            'sw.id as withdrawal_id',
            'sw.slc_no as withdrawal_slc_no',
            'sw.withdrawal_date as withdrawal_date',
            'g.name as guardian_name',
            'g.id_card_no as guardian_id_card_no',
            'p.name as legacy_parent_name',
            'p.user_information as legacy_parent_information',
        ])->paginate(10)->appends($request->all());

        $classes = Classes::where('school_id', $school_id)->get();

        return view('admin.student.student_list', compact('students', 'search', 'classes', 'class_id', 'section_id'));
    }

    public function createStudentModal()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.student.add_student', ['classes' => $classes]);
    }

    public function studentCreate(Request $request)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();
        $code = generate_student_number((int) auth()->user()->school_id, 'admission');
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');

            $imageName = time() . '.' . $data['photo']->extension();

            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $photo = '';
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );

        $data['user_information'] = json_encode($info);
        $duplicate_user_check = User::get()->where('email', $data['email']);

        if (count($duplicate_user_check) == 0) {

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'code' => generate_student_number((int) auth()->user()->school_id, 'admission'),
                'role_id' => '7',
                'school_id' => auth()->user()->school_id,
                'user_information' => $data['user_information'],
                'status' => 1,
            ]);
        } else {
            return redirect()->back()->with('error', 'Email was already taken.');
        }
        if (!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))) {
            Mail::to($data['email'])->send(new NewUserEmail($data));
        }
        return redirect()->back()->with('message', 'You have successfully add student.');
    }

    public function studentIdCardGenerate($id)
    {
        $student_details = (new CommonController)->get_student_details_by_id($id);
        return view('admin.student.id_card', ['student_details' => $student_details]);
    }
    public function studentProfile($id)
    {
        $student_details = (new CommonController)->get_student_details_by_id($id);
        $withdrawal = StudentWithdrawal::where('school_id', auth()->user()->school_id)
            ->where('student_id', (int) $id)
            ->first();

        $createdBy = null;
        if (!empty($withdrawal) && !empty($withdrawal->created_by)) {
            $createdBy = User::find($withdrawal->created_by);
        }

        return view('admin.student.student_profile', [
            'student_details' => $student_details,
            'withdrawal' => $withdrawal,
            'withdrawal_created_by' => $createdBy,
        ]);
    }

    /**
     * Full student profile page (academics, fees, attendance, withdrawal).
     */
    public function studentFullProfile($id)
    {
        $studentId = (int) $id;
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');

        $student = User::where('id', $studentId)
            ->where('school_id', $schoolId)
            ->where('role_id', 7)
            ->firstOrFail();

        $student_details = (new CommonController)->get_student_details_by_id($studentId);

        $withdrawal = StudentWithdrawal::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->first();

        // Attendance summary
        $attendanceBase = DailyAttendances::where('school_id', $schoolId)->where('student_id', $studentId);
        $attendance = [
            'overall_present' => (clone $attendanceBase)->where('status', 1)->count(),
            'overall_absent' => (clone $attendanceBase)->where('status', 0)->count(),
            'session_present' => (clone $attendanceBase)->where('session_id', $activeSession)->where('status', 1)->count(),
            'session_absent' => (clone $attendanceBase)->where('session_id', $activeSession)->where('status', 0)->count(),
        ];

        // Fee summary
        $feeBase = StudentFeeManager::where('school_id', $schoolId)->where('student_id', $studentId);
        $fees = [
            'invoice_count' => (clone $feeBase)->count(),
            'total_amount' => (int) (clone $feeBase)->sum('total_amount'),
            'paid_amount' => (int) (clone $feeBase)->sum('paid_amount'),
        ];
        $fees['due_amount'] = max(0, $fees['total_amount'] - $fees['paid_amount']);
        $recent_invoices = (clone $feeBase)->orderByDesc('id')->limit(10)->get();

        // Academics (gradebook)
        $gradebookRows = Gradebook::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->orderByDesc('timestamp')
            ->limit(30)
            ->get();

        $examCategoryNames = ExamCategory::where('school_id', $schoolId)->pluck('name', 'id')->toArray();
        $sessionNames = Session::where('school_id', $schoolId)->pluck('session_title', 'id')->toArray();

        $subjectIds = [];
        foreach ($gradebookRows as $row) {
            $m = json_decode((string) $row->marks, true);
            if (is_array($m)) {
                $subjectIds = array_merge($subjectIds, array_keys($m));
            }
        }
        $subjectIds = array_values(array_unique(array_map('intval', $subjectIds)));
        $subjectNames = !empty($subjectIds)
            ? Subject::whereIn('id', $subjectIds)->pluck('name', 'id')->toArray()
            : [];

        return view('admin.student.full_profile', compact(
            'student',
            'student_details',
            'withdrawal',
            'attendance',
            'fees',
            'recent_invoices',
            'gradebookRows',
            'examCategoryNames',
            'sessionNames',
            'subjectNames'
        ));
    }

    public function studentEditModal($id)
    {
        $user = User::find($id);
        $student_details = (new CommonController)->get_student_details_by_id($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.student.edit_student', ['user' => $user, 'student_details' => $student_details, 'classes' => $classes]);
    }

    public function studentUpdate(Request $request, $id)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
            $user_information = User::where('id', $id)->value('user_information');
            $old_photo = $user_information ? (json_decode($user_information)->photo ?? '') : '';

            $imageName = time() . '.' . $data['photo']->extension();

            delete_upload_file('assets/uploads/user-images', $old_photo);
            $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

            $photo  = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if ($file_name != '') {
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }
        $info = array(
            'gender' => $data['gender'],
            'blood_group' => $data['blood_group'],
            'birthday' => strtotime($data['birthday']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'photo' => $photo
        );
        $data['user_information'] = json_encode($info);
        User::where('id', $id)->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'user_information' => $data['user_information'],
        ]);

        Enrollment::where('user_id', $id)->update([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
        ]);

        return redirect()->back()->with('message', 'You have successfully update student.');
    }

    public function studentDelete($id)
    {
        $enroll = Enrollment::where('user_id', $id)->first();
        $enroll->delete();

        $fee_history = StudentFeeManager::get()->where('student_id', $id);
        $fee_history->map->delete();

        $attendances = DailyAttendances::get()->where('student_id', $id);
        $attendances->map->delete();

        $book_issues = BookIssue::get()->where('student_id', $id);
        $book_issues->map->delete();

        $gradebooks = Gradebook::get()->where('student_id', $id);
        $gradebooks->map->delete();

        $payments = Payments::get()->where('user_id', $id);
        $payments->map->delete();

        $payment_history = PaymentHistory::get()->where('user_id', $id);
        $payment_history->map->delete();


        $user = User::find($id);
        $user->delete();

        $students = User::get()->where('role_id', 7);
        return redirect()->back()->with('message', 'Student removed successfully.');
    }

    /**
     * Student withdrawals list (SLC records).
     */
    public function studentWithdrawalsList(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;
        $search = (string) ($request->input('search') ?? '');

        $query = DB::table('student_withdrawals as sw')
            ->join('users as s', 's.id', '=', 'sw.student_id')
            ->leftJoin('classes as c', 'c.id', '=', 'sw.class_id')
            ->leftJoin('sections as sec', 'sec.id', '=', 'sw.section_id')
            ->where('sw.school_id', $schoolId)
            ->orderByDesc('sw.id');

        if ($search !== '') {
            $digits = normalize_id_card_no($search);
            $query->where(function ($q) use ($search, $digits) {
                $q->where('s.name', 'LIKE', "%{$search}%")
                    ->orWhere('s.email', 'LIKE', "%{$search}%")
                    ->orWhere('sw.slc_no', 'LIKE', "%{$search}%")
                    ->orWhere('sw.admission_no', 'LIKE', "%{$search}%")
                    ->orWhere('sw.enrollment_no', 'LIKE', "%{$search}%")
                    ->orWhere('sw.father_name', 'LIKE', "%{$search}%")
                    ->orWhere('sw.father_cnic', 'LIKE', "%{$search}%");
                if ($digits !== '') {
                    $q->orWhere('sw.father_cnic', 'LIKE', "%{$digits}%");
                }
            });
        }

        $withdrawals = $query->select([
            'sw.*',
            's.name as student_name',
            's.email as student_email',
            'c.name as class_name',
            'sec.name as section_name',
        ])->paginate(15)->appends($request->all());

        return view('admin.student_withdrawals.index', compact('withdrawals', 'search'));
    }

    /**
     * Withdraw modal for a student (create SLC record).
     */
    public function studentWithdrawalModal($id)
    {
        $studentId = (int) $id;
        $schoolId = (int) auth()->user()->school_id;

        $student = User::where('id', $studentId)
            ->where('school_id', $schoolId)
            ->where('role_id', 7)
            ->firstOrFail();

        $existing = StudentWithdrawal::where('school_id', $schoolId)->where('student_id', $studentId)->first();

        $studentDetails = (new CommonController)->get_student_details_by_id($studentId);

        // IMPORTANT: Don't increment sequences just by opening the modal.
        // We show a preview using (last_seq + 1) without updating the counter.
        $defaultSlcNo = '';
        if (empty($existing)) {
            $pattern = (string) (DB::table('schools')->where('id', $schoolId)->value('slc_number_pattern') ?? '');
            if ($pattern === '') {
                $pattern = 'SLC-{YYYY}-{SEQ:4}';
            }

            $year = (int) date('Y');
            $yy = substr((string) $year, -2);
            $mm = date('m');

            $last = (int) (DB::table('student_number_sequences')
                ->where('school_id', $schoolId)
                ->where('type', 'slc')
                ->where('year', $year)
                ->value('last_seq') ?? 0);
            $next = $last + 1;

            $pad = 4;
            if (preg_match('/\{SEQ:(\d{1,2})\}/', $pattern, $m)) {
                $n = (int) $m[1];
                $pad = ($n >= 1 && $n <= 12) ? $n : 4;
            }
            $seq = str_pad((string) $next, $pad, '0', STR_PAD_LEFT);
            $out = str_replace(['{YYYY}', '{YY}', '{MM}'], [(string) $year, (string) $yy, (string) $mm], $pattern);
            $defaultSlcNo = preg_replace('/\{SEQ:\d{1,2}\}/', $seq, $out, 1);
        }

        return view('admin.student_withdrawals.withdrawal_modal', compact('student', 'studentDetails', 'existing', 'defaultSlcNo'));
    }

    /**
     * Store withdrawal + disable account.
     */
    public function studentWithdrawalStore(Request $request, $id)
    {
        $studentId = (int) $id;
        $schoolId = (int) auth()->user()->school_id;

        $student = User::where('id', $studentId)
            ->where('school_id', $schoolId)
            ->where('role_id', 7)
            ->firstOrFail();

        $existing = StudentWithdrawal::where('school_id', $schoolId)->where('student_id', $studentId)->first();
        if (!empty($existing)) {
            return redirect()->back()->with('error', 'This student is already withdrawn.');
        }

        $validated = $request->validate([
            'slc_no' => ['nullable', 'string', 'max:50'],
            'withdrawal_date' => ['required', 'date'],
            'slc_issue_date' => ['nullable', 'date'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'dues_cleared' => ['nullable'],
        ]);

        $enrol = Enrollment::where('user_id', $studentId)->first();
        $studentDetails = (new CommonController)->get_student_details_by_id($studentId);

        $slcNo = trim((string) ($validated['slc_no'] ?? ''));
        if ($slcNo === '') {
            $slcNo = generate_student_number($schoolId, 'slc');
        }
        $existsSlcNo = StudentWithdrawal::where('school_id', $schoolId)->where('slc_no', $slcNo)->exists();
        if ($existsSlcNo) {
            return redirect()->back()->with('error', 'SLC number already exists. Please try again.');
        }

        StudentWithdrawal::create([
            'school_id' => $schoolId,
            'student_id' => $studentId,
            'enrollment_id' => $enrol->id ?? null,
            'class_id' => $enrol->class_id ?? null,
            'section_id' => $enrol->section_id ?? null,
            'session_id' => $enrol->session_id ?? null,
            'admission_no' => $studentDetails['admission_no'] ?? ($student->code ?? null),
            'enrollment_no' => $studentDetails['enrollment_no'] ?? null,
            'father_name' => $studentDetails['father_name'] ?? null,
            'father_cnic' => $studentDetails['parent_id_card'] ?? null,
            'slc_no' => $slcNo,
            'withdrawal_date' => $validated['withdrawal_date'],
            'slc_issue_date' => $validated['slc_issue_date'] ?? null,
            'reason' => $validated['reason'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'dues_cleared' => !empty($request->input('dues_cleared')) ? 1 : 0,
            'created_by' => auth()->id(),
        ]);

        // Disable student account after withdrawal
        User::where('id', $studentId)->update(['account_status' => 'disable']);

        return redirect()->back()->with('message', 'Student withdrawn and SLC record created successfully.');
    }

    /**
     * Print SLC certificate.
     */
    public function studentWithdrawalPrint($id)
    {
        $withdrawalId = (int) $id;
        $schoolId = (int) auth()->user()->school_id;

        $withdrawal = StudentWithdrawal::where('id', $withdrawalId)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        $student = User::find($withdrawal->student_id);
        $school = School::find($schoolId);

        return view('admin.student_withdrawals.slc_print', compact('withdrawal', 'student', 'school'));
    }


    /**
     * Show the teacher permission form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function teacherPermission()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::where('role_id', 3)
            ->where('school_id', auth()->user()->school_id)
            ->get();
        return view('admin.permission.index', ['classes' => $classes, 'teachers' => $teachers]);
    }

    public function teacherPermissionList($value = "")
    {
        $data = explode('-', $value);
        $class_id = $data[0];
        $section_id = $data[1];
        $teachers = User::where('role_id', 3)
            ->where('school_id', auth()->user()->school_id)
            ->get();
        return view('admin.permission.list', ['teachers' => $teachers, 'class_id' => $class_id, 'section_id' => $section_id]);
    }

    public function teacherPermissionUpdate(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $teacher_id = $data['teacher_id'];
        $column_name = $data['column_name'];
        $value = $data['value'];

        $check_row = TeacherPermission::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('teacher_id', $teacher_id)
            ->where('school_id', auth()->user()->school_id)
            ->get();

        if (count($check_row) > 0) {

            TeacherPermission::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('teacher_id', $teacher_id)
                ->where('school_id', auth()->user()->school_id)
                ->update([
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'school_id' => auth()->user()->school_id,
                    'teacher_id' => $teacher_id,
                    $column_name => $data['value'],
                ]);
        } else {
            TeacherPermission::create([
                'class_id' => $class_id,
                'section_id' => $section_id,
                'school_id' => auth()->user()->school_id,
                'teacher_id' => $teacher_id,
                $column_name => 1,
            ]);
        }
    }


    /**
     * Show the offline_admission form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function offlineAdmissionForm($type = '')
    {
        $data['parents'] = User::where(['role_id' => 6, 'school_id' => auth()->user()->school_id])->get();
        $data['departments'] = Department::get()->where('school_id', auth()->user()->school_id);
        $data['classes'] = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.offline_admission.offline_admission', ['aria_expand' => $type, 'data' => $data]);
    }

    public function offlineAdmissionCreate(Request $request)
    {
        $request->validate([
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $package = Subscription::where('school_id', auth()->user()->school_id)->latest()->first();


        $student_limit = $package->studentLimit;

        $student_count = User::where(['role_id' => 7, 'school_id' => auth()->user()->school_id])->count();



        if ($student_limit == 'unlimited' || $student_limit > $student_count) {

            $data = $request->all();
            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo');

                $imageName = time() . '.' . $data['photo']->extension();

                $data['photo']->storeAs('assets/uploads/user-images', $imageName, 'public');

                $photo  = $imageName;
            } else {
                $photo = '';
            }

            $info = array(
                'gender' => $data['gender'],
                'blood_group' => $data['blood_group'],
                'birthday' => strtotime($data['eDefaultDateRange']),
                'phone' => $data['phone'],
                'address' => $data['address'],
                'photo' => $photo,
                'father_name' => $data['father_name'] ?? '',
                'parent_id_card' => $data['parent_id_card'] ?? ''
            );
            $data['user_information'] = json_encode($info);

            $duplicate_user_check = User::get()->where('email', $data['email']);

            if (count($duplicate_user_check) == 0) {
                $parent_id = $data['parent_id'] ?? null;

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'code' => generate_student_number((int) auth()->user()->school_id, 'admission'),
                    'role_id' => '7',
                    'parent_id' => $parent_id,
                    'school_id' => auth()->user()->school_id,
                    'user_information' => $data['user_information'],
                    'status' => 1,
                ]);

                Enrollment::create([
                    'user_id' => $user->id,
                    'enrollment_no' => generate_student_number((int) auth()->user()->school_id, 'enrollment'),
                    'class_id' => $data['class_id'],
                    'section_id' => $data['section_id'],
                    'school_id' => auth()->user()->school_id,
                    'session_id' => $active_session,
                ]);

                // Guardian identity (track family by CNIC + name)
                $parent_id_card = (string) ($data['parent_id_card'] ?? '');
                $father_name = (string) ($data['father_name'] ?? '');
                if ($parent_id_card !== '' && !is_valid_id_card_no($parent_id_card)) {
                    return redirect()->back()->with('error', 'Parent ID card number must be exactly 13 digits.');
                }
                $school_id = (int) auth()->user()->school_id;

                $normalized = normalize_id_card_no($parent_id_card);
                $guardian = null;

                if ($normalized !== '') {
                    $guardian = Guardian::where('school_id', $school_id)
                        ->where('id_card_no_normalized', $normalized)
                        ->first();
                } elseif (!empty($parent_id)) {
                    $guardian = Guardian::where('school_id', $school_id)
                        ->where('user_id', $parent_id)
                        ->first();
                }

                if (empty($guardian)) {
                    $guardian = Guardian::create([
                        'school_id' => $school_id,
                        'user_id' => $parent_id,
                        'name' => $father_name !== '' ? $father_name : null,
                        'id_card_no' => $parent_id_card !== '' ? $parent_id_card : null,
                        'id_card_no_normalized' => $normalized !== '' ? $normalized : null,
                    ]);
                } else {
                    $updates = [];
                    if (empty($guardian->user_id) && !empty($parent_id)) {
                        $updates['user_id'] = $parent_id;
                    }
                    if ((empty($guardian->name) || $guardian->name === null) && $father_name !== '') {
                        $updates['name'] = $father_name;
                    }
                    if ((empty($guardian->id_card_no) || $guardian->id_card_no === null) && $parent_id_card !== '') {
                        $updates['id_card_no'] = $parent_id_card;
                    }
                    if ((empty($guardian->id_card_no_normalized) || $guardian->id_card_no_normalized === null) && $normalized !== '') {
                        $updates['id_card_no_normalized'] = $normalized;
                    }
                    if (!empty($updates)) {
                        Guardian::where('id', $guardian->id)->update($updates);
                    }
                }

                StudentGuardian::firstOrCreate(
                    ['student_id' => $user->id, 'guardian_id' => $guardian->id, 'relation' => 'father'],
                    ['is_primary' => 1, 'is_fee_payer' => 1]
                );

                if (!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))) {
                    Mail::to($data['email'])->send(new NewUserEmail($data));
                }
                return redirect()->back()->with('message', 'Admission successfully done.');
            } else {

                return redirect()->back()->with('error', 'Sorry this email has been taken');
            }
        } else {
            return redirect()->back()->with('error', 'Your students limit out.Please upgrade to add more students');
        }
    }

    public function offlineAdmissionBulkCreate(Request $request)
    {
        $data = $request->all();

        $duplication_counter = 0;
        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $department_id = $data['department_id'];

        $students_name = $data['name'];
        $students_email = $data['email'];
        $students_password = $data['password'];
        $students_gender = $data['gender'];
        $students_parent = $data['parent_id'];
        $students_father_name = $data['father_name'] ?? [];
        $students_parent_id_card = $data['parent_id_card'] ?? [];

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        foreach ($students_name as $key => $value) {
            $duplicate_user_check = User::get()->where('email', $students_email[$key]);

            if (count($duplicate_user_check) == 0) {
                $father_name = $students_father_name[$key] ?? '';
                $parent_id_card = $students_parent_id_card[$key] ?? '';
                $parent_id = $students_parent[$key] ?? null;

                if ($parent_id_card !== '' && !is_valid_id_card_no($parent_id_card)) {
                    // Skip this row but keep processing others
                    $duplication_counter++;
                    continue;
                }

                $info = array(
                    'gender' => $students_gender[$key],
                    'blood_group' => '',
                    'birthday' => '',
                    'phone' => '',
                    'address' => '',
                    'photo' => '',
                    'father_name' => $father_name,
                    'parent_id_card' => $parent_id_card
                );
                $data['user_information'] = json_encode($info);

                $user = User::create([
                    'name' => $students_name[$key],
                    'email' => $students_email[$key],
                    'password' => Hash::make($students_password[$key]),
                    'code' => generate_student_number((int) auth()->user()->school_id, 'admission'),
                    'role_id' => '7',
                    'parent_id' => $parent_id,
                    'school_id' => auth()->user()->school_id,
                    'user_information' => $data['user_information'],
                    'status' => 1,
                ]);

                // Guardian identity (track family by CNIC + name)
                $school_id = (int) auth()->user()->school_id;
                $normalized = normalize_id_card_no($parent_id_card);
                $guardian = null;

                if ($normalized !== '') {
                    $guardian = Guardian::where('school_id', $school_id)
                        ->where('id_card_no_normalized', $normalized)
                        ->first();
                } elseif (!empty($parent_id)) {
                    $guardian = Guardian::where('school_id', $school_id)
                        ->where('user_id', $parent_id)
                        ->first();
                }

                if (empty($guardian)) {
                    $guardian = Guardian::create([
                        'school_id' => $school_id,
                        'user_id' => $parent_id,
                        'name' => $father_name !== '' ? $father_name : null,
                        'id_card_no' => $parent_id_card !== '' ? $parent_id_card : null,
                        'id_card_no_normalized' => $normalized !== '' ? $normalized : null,
                    ]);
                } else {
                    $updates = [];
                    if (empty($guardian->user_id) && !empty($parent_id)) {
                        $updates['user_id'] = $parent_id;
                    }
                    if ((empty($guardian->name) || $guardian->name === null) && $father_name !== '') {
                        $updates['name'] = $father_name;
                    }
                    if ((empty($guardian->id_card_no) || $guardian->id_card_no === null) && $parent_id_card !== '') {
                        $updates['id_card_no'] = $parent_id_card;
                    }
                    if ((empty($guardian->id_card_no_normalized) || $guardian->id_card_no_normalized === null) && $normalized !== '') {
                        $updates['id_card_no_normalized'] = $normalized;
                    }
                    if (!empty($updates)) {
                        Guardian::where('id', $guardian->id)->update($updates);
                    }
                }

                StudentGuardian::firstOrCreate(
                    ['student_id' => $user->id, 'guardian_id' => $guardian->id, 'relation' => 'father'],
                    ['is_primary' => 1, 'is_fee_payer' => 1]
                );


                Enrollment::create([
                    'user_id' => $user->id,
                    'enrollment_no' => generate_student_number((int) auth()->user()->school_id, 'enrollment'),
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'school_id' => auth()->user()->school_id,
                    'department_id' => $department_id,
                    'session_id' => $active_session,
                ]);
            } else {
                $duplication_counter++;
            }
        }

        if ($duplication_counter > 0) {

            return redirect()->back()->with('warning', 'Some of the emails have been taken.');
        } else {

            return redirect()->back()->with('message', 'Students added successfully');
        }
    }

    public function offlineAdmissionExcelCreate(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $school_id = auth()->user()->school_id;
        $session_id = get_school_settings(auth()->user()->school_id)->value('running_session');
        $package = Subscription::where('school_id', auth()->user()->school_id)->first();

        $student_limit = $package->studentLimit;

        $student_count = User::where(['role_id' => 7, 'school_id' => auth()->user()->school_id])->count();


        $file = $data['csv_file'];
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

            // Upload file
            $file->move(public_path('assets/csv_file/'), $filename);

            // In case the uploaded file path is to be stored in the database
            $filepath = url('public/assets/csv_file/' . $filename);
        }

        if (($handle = fopen($filepath, 'r')) !== FALSE) { // Check the resource is valid
            $count = 0;
            $duplication_counter = 0;
            $invalid_id_card_counter = 0;

            while (($all_data = fgetcsv($handle, 1000, ",")) !== FALSE) { // Check opening the file is OK!
                if ($student_limit == 'unlimited' || $student_limit > $student_count) {
                    if ($count > 0) {

                        $duplicate_user_check = User::get()->where('email', $all_data[1]);

                        if (count($duplicate_user_check) == 0) {
                            $father_name = $all_data[8] ?? '';
                            $parent_id_card = $all_data[9] ?? '';
                            $parent_id = $all_data[10] ?? null;

                            if ($parent_id_card !== '' && !is_valid_id_card_no($parent_id_card)) {
                                $invalid_id_card_counter++;
                                $count++;
                                continue;
                            }

                            if ($parent_id === '') {
                                $parent_id = null;
                            }

                            if (empty($parent_id) && !empty($parent_id_card)) {
                                $parent_id = User::where('role_id', 6)
                                    ->where('school_id', $school_id)
                                    ->where('user_information', 'like', '%"id_card_no":"' . addslashes((string) $parent_id_card) . '"%')
                                    ->value('id');
                            }

                            $info = array(
                                'gender' => $all_data[5],
                                'blood_group' => $all_data[4],
                                'birthday' => strtotime($all_data[6]),
                                'phone' => $all_data[3],
                                'address' => $all_data[7],
                                'photo' => '',
                                'father_name' => $father_name,
                                'parent_id_card' => $parent_id_card
                            );

                            $data['user_information'] = json_encode($info);

                            $user = User::create([
                                'name' => $all_data[0],
                                'email' => $all_data[1],
                                'password' => Hash::make($all_data[2]),
                                'code' => generate_student_number((int) auth()->user()->school_id, 'admission'),
                                'role_id' => '7',
                                'school_id' => $school_id,
                                'parent_id' => $parent_id,
                                'user_information' => $data['user_information'],
                                'status' => 1,
                            ]);

                            // Guardian identity (track family by CNIC + name)
                            $normalized = normalize_id_card_no($parent_id_card);
                            $guardian = null;

                            if ($normalized !== '') {
                                $guardian = Guardian::where('school_id', $school_id)
                                    ->where('id_card_no_normalized', $normalized)
                                    ->first();
                            } elseif (!empty($parent_id)) {
                                $guardian = Guardian::where('school_id', $school_id)
                                    ->where('user_id', $parent_id)
                                    ->first();
                            }

                            if (empty($guardian)) {
                                $guardian = Guardian::create([
                                    'school_id' => $school_id,
                                    'user_id' => $parent_id,
                                    'name' => $father_name !== '' ? $father_name : null,
                                    'id_card_no' => $parent_id_card !== '' ? $parent_id_card : null,
                                    'id_card_no_normalized' => $normalized !== '' ? $normalized : null,
                                ]);
                            } else {
                                $updates = [];
                                if (empty($guardian->user_id) && !empty($parent_id)) {
                                    $updates['user_id'] = $parent_id;
                                }
                                if ((empty($guardian->name) || $guardian->name === null) && $father_name !== '') {
                                    $updates['name'] = $father_name;
                                }
                                if ((empty($guardian->id_card_no) || $guardian->id_card_no === null) && $parent_id_card !== '') {
                                    $updates['id_card_no'] = $parent_id_card;
                                }
                                if ((empty($guardian->id_card_no_normalized) || $guardian->id_card_no_normalized === null) && $normalized !== '') {
                                    $updates['id_card_no_normalized'] = $normalized;
                                }
                                if (!empty($updates)) {
                                    Guardian::where('id', $guardian->id)->update($updates);
                                }
                            }

                            StudentGuardian::firstOrCreate(
                                ['student_id' => $user->id, 'guardian_id' => $guardian->id, 'relation' => 'father'],
                                ['is_primary' => 1, 'is_fee_payer' => 1]
                            );


                            Enrollment::create([
                                'user_id' => $user->id,
                                'enrollment_no' => generate_student_number((int) auth()->user()->school_id, 'enrollment'),
                                'class_id' => $class_id,
                                'section_id' => $section_id,
                                'school_id' => $school_id,
                                'session_id' => $session_id,
                            ]);
                        } else {
                            $duplication_counter++;
                        }

                        // check email duplication

                    }
                } else {
                    return redirect()->back()->with('error', 'Your students limit out.Please upgrade to add more students.');
                }
                $count++;
            }

            fclose($handle);
        }

        if ($invalid_id_card_counter > 0) {
            return redirect()->back()->with('warning', 'Some rows were skipped due to invalid parent ID card number (must be 13 digits).');
        }

        if ($duplication_counter > 0) {

            return redirect()->back()->with('warning', 'Some of the emails have been taken.');
        } else {

            return redirect()->back()->with('message', 'Students added successfully');
        }
    }


    /**
     * Show the exam category list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function examCategoryList()
    {
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.exam_category.exam_category', ['exam_categories' => $exam_categories]);
    }

    public function createExamCategory()
    {
        return view('admin.exam_category.create');
    }

    public function examCategoryCreate(Request $request)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        ExamCategory::create([
            'name' => $data['name'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
            'timestamp' => strtotime(date('Y-m-d')),
        ]);
        return redirect()->back()->with('message', 'Exam category created successfully.');
    }

    public function editExamCategory($id = '')
    {
        $exam_category = ExamCategory::find($id);
        return view('admin.exam_category.edit', ['exam_category' => $exam_category]);
    }

    public function examCategoryUpdate(Request $request, $id)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        ExamCategory::where('id', $id)->update([
            'name' => $data['name'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
            'timestamp' => strtotime(date('Y-m-d')),
        ]);
        return redirect()->back()->with('message', 'Exam category updated successfully.');
    }

    public function examCategoryDelete($id = '')
    {
        $exam_category = ExamCategory::find($id);
        $exam_category->delete();
        return redirect()->back()->with('message', 'You have successfully delete exam category.');
    }


    /**
     * Show the exam list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function offlineExamList()
    {
        $id = "all";
        $exams = Exam::get()->where('exam_type', 'offline')->where('school_id', auth()->user()->school_id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.offline_exam_list', ['exams' => $exams, 'classes' => $classes, 'id' => $id]);
    }

    public function offlineExamExport($id = "")
    {
        if ($id != "all") {
            $exams = Exam::where([
                'exam_type' => 'offline',
                'class_id' => $id
            ])->get();
        } else {
            $exams = Exam::get()->where('exam_type', 'offline');
        }
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.offline_exam_export', ['exams' => $exams, 'classes' => $classes]);
    }

    public function classWiseOfflineExam($id)
    {
        $exams = Exam::where([
            'exam_type' => 'offline',
            'class_id' => $id
        ])->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.exam_list', ['exams' => $exams, 'classes' => $classes, 'id' => $id]);
    }

    public function createOfflineExam()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.add_offline_exam', ['classes' => $classes, 'exam_categories' => $exam_categories]);
    }

    public function classWiseSubject($id)
    {
        $subjects = Subject::get()->where('class_id', $id);
        $options = '<option value="">' . 'Select a subject' . '</option>';
        foreach ($subjects as $subject):
            $options .= '<option value="' . $subject->id . '">' . $subject->name . '</option>';
        endforeach;
        echo $options;
    }

    public function offlineExamCreate(Request $request)
    {

        // Retrieve request data
        $data = $request->input('class_room_id');
        $startingTime =  strtotime($request->starting_date . '' . $request->starting_time);
        $endingTime = strtotime($request->ending_date . '' . $request->ending_time);


        // Check if the room is occupied for the specified time range
        $occupiedExams = Exam::where('room_number', $data)
            ->where(function ($query) use ($startingTime, $endingTime) {
                $query->whereBetween('starting_time', [$startingTime, $endingTime])
                    ->orWhereBetween('ending_time', [$startingTime, $endingTime])
                    ->orWhere(function ($query) use ($startingTime, $endingTime) {
                        $query->where('starting_time', '<=', $startingTime)
                            ->where('ending_time', '>=', $endingTime);
                    });
            })
            ->get();
        // Return response based on room availability
        if (count($occupiedExams) != 0) {
            return redirect()->back()->with(['warning' => 'The room is occupied for the specified time range'], 409);
        } else {
            $data = $request->all();
            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
            $exam_category = ExamCategory::find($data['exam_category_id']);
            Exam::create([
                'name' => $exam_category->name,
                'exam_category_id' => $data['exam_category_id'],
                'exam_type' => 'offline',
                'room_number' => $data['class_room_id'],
                'starting_time' => strtotime($data['starting_date'] . '' . $data['starting_time']),
                'ending_time' => strtotime($data['ending_date'] . '' . $data['ending_time']),
                'total_marks' => $data['total_marks'],
                'status' => 'pending',
                'class_id' => $data['class_id'],
                'subject_id' => $data['subject_id'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

            return redirect()->back()->with(['message' => 'You have successfully create exam'], 200);
        }
    }

    public function editOfflineExam($id)
    {
        $exam = Exam::find($id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $subjects = Subject::get()->where('class_id', $exam->class_id);
        $exam_categories = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.examination.edit_offline_exam', ['exam' => $exam, 'classes' => $classes, 'subjects' => $subjects, 'exam_categories' => $exam_categories]);
    }

    public function offlineExamUpdate(Request $request, $id)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $exam_category = ExamCategory::find($data['exam_category_id']);
        Exam::where('id', $id)->update([
            'name' => $exam_category->name,
            'exam_category_id' => $data['exam_category_id'],
            'exam_type' => 'offline',
            'room_number' => $data['class_room_id'],
            'starting_time' => strtotime($data['starting_date'] . '' . $data['starting_time']),
            'ending_time' => strtotime($data['ending_date'] . '' . $data['ending_time']),
            'total_marks' => $data['total_marks'],
            'status' => 'pending',
            'class_id' => $data['class_id'],
            'subject_id' => $data['subject_id'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message', 'You have successfully update exam.');
    }

    public function offlineExamDelete($id)
    {
        $exam = Exam::find($id);
        $exam->delete();
        $exams = Exam::get()->where('exam_type', 'offline');
        return redirect()->back()->with('message', 'You have successfully delete exam.');
    }

    /**
     * Show the grade daily attendance.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function dailyAttendance()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        $attendance_of_students = array();
        $no_of_users = 0;

        return view('admin.attendance.daily_attendance', ['classes' => $classes, 'attendance_of_students' => $attendance_of_students, 'no_of_users' => $no_of_users]);
    }

    public function dailyAttendanceFilter(Request $request)
    {
        $data = $request->all();
        $date = '01 ' . $data['month'] . ' ' . $data['year'];
        $first_date = strtotime($date);
        $last_date = date("Y-m-t", strtotime($date));
        $last_date = strtotime($last_date);

        $page_data['attendance_date'] = strtotime($date);
        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['month'] = $data['month'];
        $page_data['year'] = $data['year'];

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $attendance_of_students = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get()->toArray();

        $students_details = Enrollment::where('class_id', $page_data['class_id'])
            ->where('section_id', $page_data['section_id'])
            ->get();


        $no_of_users = DailyAttendances::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->distinct()->count('student_id');

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.attendance.attendance_list', ['page_data' => $page_data, 'classes' => $classes, 'attendance_of_students' => $attendance_of_students, 'students_details' => $students_details, 'no_of_users' => $no_of_users]);
    }

    public function takeAttendance()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.attendance.take_attendance', ['classes' => $classes]);
    }

    public function studentListAttendance(Request $request)
    {
        $data = $request->all();

        $page_data['attendance_date'] = $data['date'];
        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];

        return view('admin.attendance.student', ['page_data' => $page_data]);
    }

    public function attendanceTake(Request $request)
    {
        $att_data = $request->all();

        $students = $att_data['student_id'];
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['timestamp'] = strtotime($att_data['date']);
        $data['class_id'] = $att_data['class_id'];
        $data['section_id'] = $att_data['section_id'];
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        $check_data = DailyAttendances::where(['timestamp' => $data['timestamp'], 'class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'session_id' => $active_session, 'school_id' => auth()->user()->school_id])->get();
        if (count($check_data) > 0) {
            foreach ($students as $key => $student):
                $data['status'] = $att_data['status-' . $student];
                $data['student_id'] = $student;
                $attendance_id = $att_data['attendance_id'];

                if (isset($attendance_id[$key])) {

                    DailyAttendances::where('id', $attendance_id[$key])->update($data);
                } else {
                    DailyAttendances::create($data);
                }
            endforeach;
        } else {
            foreach ($students as $student):
                $data['status'] = $att_data['status-' . $student];
                $data['student_id'] = $student;

                DailyAttendances::create($data);

            endforeach;
        }

        return redirect()->back()->with('message', 'Student attendance updated successfully.');
    }

    public function dailyAttendanceFilter_csv(Request $request)
    {

        $data = $request->all();

        $store_get_data = array_keys($data);


        $data['month'] = substr($store_get_data[0], 0, 3);
        $data['year'] = substr($store_get_data[0], 4, 4);
        $data['role_id'] = substr($store_get_data[0], 9, 5);

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');


        $date = '01 ' . $data['month'] . ' ' . $data['year'];


        $first_date = strtotime($date);

        $last_date = date("Y-m-t", strtotime($date));
        $last_date = strtotime($last_date);

        $page_data['month'] = $data['month'];
        $page_data['year'] = $data['year'];
        $page_data['attendance_date'] = $first_date;
        $no_of_users = 0;






        $no_of_users = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id,  'session_id' => $active_session])->distinct()->count('student_id');
        $attendance_of_students = DailyAttendances::whereBetween('timestamp', [$first_date, $last_date])->where(['school_id' => auth()->user()->school_id,  'session_id' => $active_session])->get()->toArray();


        $csv_content = "Student" . "/" . get_phrase('Date');
        $number_of_days = date('m', $page_data['attendance_date']) == 2 ? (date('Y', $page_data['attendance_date']) % 4 ? 28 : (date('m', $page_data['attendance_date']) % 100 ? 29 : (date('m', $page_data['attendance_date']) % 400 ? 28 : 29))) : ((date('m', $page_data['attendance_date']) - 1) % 7 % 2 ? 30 : 31);
        for ($i = 1; $i <= $number_of_days; $i++) {
            $csv_content .= ',' . get_phrase($i);
        }


        $file = "Attendence_report.csv";


        $student_id_count = 0;


        foreach (array_slice($attendance_of_students, 0, $no_of_users) as $attendance_of_student) {
            $csv_content .= "\n";

            $user_details = (new CommonController)->get_user_by_id_from_user_table($attendance_of_student['student_id']);
            if (date('m', $page_data['attendance_date']) == date('m', $attendance_of_student['timestamp'])) {



                if ($student_id_count != $attendance_of_student['student_id']) {


                    $csv_content .= $user_details['name'] . ',';


                    for ($i = 1; $i <= $number_of_days; $i++) {
                        $page_data['date'] = $i . ' ' . $page_data['month'] . ' ' . $page_data['year'];
                        $timestamp = strtotime($page_data['date']);

                        $attendance_by_id = DailyAttendances::where(['student_id' => $attendance_of_student['student_id'], 'school_id' => auth()->user()->school_id, 'timestamp' => $timestamp])->first();
                        if (isset($attendance_by_id->status) && $attendance_by_id->status == 1) {
                            $csv_content .= "P,";
                        } elseif (isset($attendance_by_id->status) && $attendance_by_id->status == 0) {
                            $csv_content .= "A,";
                        } else {
                            $csv_content .= ",";
                        }


                        if ($i == $number_of_days) {
                            $csv_content = substr_replace($csv_content, "", -1);
                        }
                    }
                }

                $student_id_count = $attendance_of_student['student_id'];
            }
        }

        $txt = fopen($file, "w") or die("Unable to open file!");
        fwrite($txt, $csv_content);
        fclose($txt);

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $file);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        header("Content-type: text/csv");
        readfile($file);
    }

    /**
     * Show the routine.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function routine()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.routine.routine', ['classes' => $classes]);
    }

    public function routineList(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.routine.routine_list', ['class_id' => $class_id, 'section_id' => $section_id, 'classes' => $classes]);
    }

    public function addRoutine()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::where(['role_id' => 3, 'school_id' => auth()->user()->school_id])->get();
        $class_rooms = ClassRoom::get()->where('school_id', auth()->user()->school_id);
        return view('admin.routine.add_routine', ['classes' => $classes, 'teachers' => $teachers, 'class_rooms' => $class_rooms]);
    }

    public function routineAdd(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Routine::create([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'teacher_id' => $data['teacher_id'],
            'room_id' => $data['class_room_id'],
            'day' => $data['day'],
            'starting_hour' => $data['starting_hour'],
            'starting_minute' => $data['starting_minute'],
            'ending_hour' => $data['ending_hour'],
            'ending_minute' => $data['ending_minute'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect('/admin/routine/list?class_id=' . $data['class_id'] . '&section_id=' . $data['section_id'])->with('message', 'You have successfully create a class routine.');
    }

    public function routineEditModal($id)
    {
        $routine = Routine::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::where(['role_id' => 3, 'school_id' => auth()->user()->school_id])->get();
        $class_rooms = ClassRoom::get()->where('school_id', auth()->user()->school_id);
        return view('admin.routine.edit_routine', ['routine' => $routine, 'classes' => $classes, 'teachers' => $teachers, 'class_rooms' => $class_rooms]);
    }

    public function routineUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Routine::where('id', $id)->update([
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'teacher_id' => $data['teacher_id'],
            'room_id' => $data['class_room_id'],
            'day' => $data['day'],
            'starting_hour' => $data['starting_hour'],
            'starting_minute' => $data['starting_minute'],
            'ending_hour' => $data['ending_hour'],
            'ending_minute' => $data['ending_minute'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message', 'You have successfully update routine.');
    }

    public function routineDelete($id)
    {
        $routine = Routine::find($id);
        $routine->delete();
        return redirect()->back()->with('message', 'You have successfully delete routine.');
    }

    /**
     * Show the syllabus.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function syllabus()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.syllabus.syllabus', ['classes' => $classes]);
    }


    public function syllabusList(Request $request)
    {
        $data = $request->all();

        $class_id = $data['class_id'];
        $section_id = $data['section_id'];
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.syllabus.syllabus_list', ['class_id' => $class_id, 'section_id' => $section_id, 'classes' => $classes]);
    }

    public function addSyllabus()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.syllabus.add_syllabus', ['classes' => $classes]);
    }

    public function syllabusAdd(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $file = $data['syllabus_file'];

        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

            $file->storeAs('assets/uploads/syllabus', $filename, 'public');

            $filepath = asset('assets/uploads/syllabus/' . $filename);
        }

        Syllabus::create([
            'title' => $data['title'],
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'file' => $filename,
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect('/admin/syllabus/list?class_id=' . $data['class_id'] . '&section_id=' . $data['section_id'])->with('message', 'You have successfully create a syllabus.');
    }

    public function syllabusEditModal($id)
    {
        $syllabus = Syllabus::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.syllabus.edit_syllabus', ['syllabus' => $syllabus, 'classes' => $classes]);
    }

    public function syllabusUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $file = $data['syllabus_file'];

        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file

            $file->storeAs('assets/uploads/syllabus', $filename, 'public');

            $filepath = asset('assets/uploads/syllabus/' . $filename);
        }

        Syllabus::where('id', $id)->update([
            'title' => $data['title'],
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'subject_id' => $data['subject_id'],
            'file' => $filename,
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect('/admin/syllabus/list?class_id=' . $data['class_id'] . '&section_id=' . $data['section_id'])->with('message', 'You have successfully update a syllabus.');
    }

    public function syllabusDelete($id)
    {
        $syllabus = Syllabus::find($id);
        $syllabus->delete();
        return redirect()->back()->with('message', 'You have successfully delete syllabus.');
    }

    /**
     * Show the gradebook.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function gradebook(Request $request)
    {

        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $exam_categories = ExamCategory::get()->where('school_id', auth()->user()->school_id);

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if (count($request->all()) > 0) {

            $data = $request->all();

            $filter_list = Gradebook::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get();

            $class_id = $data['class_id'];
            $section_id = $data['section_id'];
            $exam_category_id = $data['exam_category_id'];
            $subjects = Subject::where(['class_id' => $class_id, 'school_id' => auth()->user()->school_id])->get();
        } else {
            $filter_list = [];

            $class_id = '';
            $section_id = '';
            $exam_category_id = '';
            $subjects = '';
        }

        return view('admin.gradebook.gradebook', ['filter_list' => $filter_list, 'class_id' => $class_id, 'section_id' => $section_id, 'exam_category_id' => $exam_category_id, 'classes' => $classes, 'exam_categories' => $exam_categories, 'subjects' => $subjects]);
    }

    public function gradebookList(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $exam_wise_student_list = Gradebook::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get();
        echo view('admin.gradebook.list', ['exam_wise_student_list' => $exam_wise_student_list, 'class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session]);
    }

    public function subjectWiseMarks(Request $request, $student_id = "")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $subject_wise_mark_list = Gradebook::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'student_id' => $student_id, 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->first();

        echo view('admin.gradebook.subject_marks', ['subject_wise_mark_list' => $subject_wise_mark_list]);
    }

    public function addMark()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $exam_categories = ExamCategory::get()->where('school_id', auth()->user()->school_id);
        return view('admin.gradebook.add_mark', ['classes' => $classes, 'exam_categories' => $exam_categories]);
    }

    public function markAdd(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $subject_wise_mark_list = Gradebook::where(['class_id' => $data['class_id'], 'section_id' => $data['section_id'], 'exam_category_id' => $data['exam_category_id'], 'student_id' => $data['student_id'], 'school_id' => auth()->user()->school_id, 'session_id' => $active_session])->get();

        $result = $subject_wise_mark_list->count();

        if ($result > 0) {

            return redirect()->back()->with('message', 'Mark added successfully.');
        } else {

            $mark = array($data['subject_id'] => $data['mark']);

            $marks = json_encode($mark);

            $data['marks'] = $marks;
            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;
            $data['timestamp'] = strtotime(date('Y-m-d'));

            Gradebook::create($data);

            return redirect()->back()->with('message', 'Mark added successfully.');
        }
    }

    /**
     * Show the grade list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function marks($value = '')
    {
        $page_data['exam_categories'] = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();
        $page_data['sessions'] = Session::where('school_id', auth()->user()->school_id)->get();

        return view('admin.marks.index', $page_data);
    }

    public function marksFilter(Request $request)
    {
        $data = $request->all();

        $page_data['exam_category_id'] = $data['exam_category_id'];
        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['subject_id'] = $data['subject_id'];
        $page_data['session_id'] = $data['session_id'];

        $page_data['class_name'] = Classes::find($data['class_id'])->name;
        $page_data['section_name'] = Section::find($data['section_id'])->name;
        $page_data['subject_name'] = Subject::find($data['subject_id'])->name;
        $page_data['session_title'] = Session::find($data['session_id'])->session_title;


        $enroll_students = Enrollment::where('class_id', $page_data['class_id'])
            ->where('section_id', $page_data['section_id'])
            ->get();

        $page_data['exam_categories'] = ExamCategory::where('school_id', auth()->user()->school_id)->get();
        $page_data['classes'] = Classes::where('school_id', auth()->user()->school_id)->get();

        $exam = Exam::where('exam_type', 'offline')
            ->where('class_id', $data['class_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('session_id', $data['session_id'])
            ->where('exam_category_id', $data['exam_category_id'])
            ->where('school_id', auth()->user()->school_id)
            ->first();

        if ($exam) {
            $response = view('admin.marks.marks_list', [
                'enroll_students' => $enroll_students,
                'page_data' => $page_data,
                'exam' => $exam,
            ])->render();
            return response()->json(['status' => 'success', 'html' => $response]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No records found for the specified filter. First create exam for the selected filter.']);
        }
    }

    public function marksPdf($section_id = "", $class_id = "", $session_id = "", $exam_category_id = "", $subject_id = "")
    {

        $enroll_students = Enrollment::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->get();

        $data = [
            'enroll_students' => $enroll_students,
            'section_id' => $section_id,
            'class_id' => $class_id,
            'session_id' => $session_id,
            'exam_category_id' => $exam_category_id,
            'subject_id' => $subject_id
        ];

        $pdf = PDF::loadView('admin.marks.markPdf', $data);

        return $pdf->download('webappfix.pdf');

        // return $pdf->stream('webappfix.pdf');
    }

    /**
     * Show the grade list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function gradeList()
    {
        $grades = Grade::get()->where('school_id', auth()->user()->school_id);
        return view('admin.grade.grade_list', ['grades' => $grades]);
    }

    public function createGrade()
    {
        return view('admin.grade.add_grade');
    }

    public function gradeCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_grade_check = Grade::get()->where('name', $data['grade'])->where('school_id', auth()->user()->school_id);

        if (count($duplicate_grade_check) == 0) {
            Grade::create([
                'name' => $data['grade'],
                'grade_point' => $data['grade_point'],
                'mark_from' => $data['mark_from'],
                'mark_upto' => $data['mark_upto'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message', 'You have successfully create a new grade.');
        } else {
            return back()
                ->with('error', 'Sorry this grade already exists');
        }
    }

    public function editGrade($id)
    {
        $grade = Grade::find($id);
        return view('admin.grade.edit_grade', ['grade' => $grade]);
    }

    public function gradeUpdate(Request $request, $id)
    {
        $data = $request->all();
        Grade::where('id', $id)->update([
            'name' => $data['grade'],
            'grade_point' => $data['grade_point'],
            'mark_from' => $data['mark_from'],
            'mark_upto' => $data['mark_upto'],
            'school_id' => auth()->user()->school_id,
        ]);

        return redirect()->back()->with('message', 'You have successfully update grade.');
    }

    public function gradeDelete($id)
    {
        $grade = Grade::find($id);
        $grade->delete();
        $grades = Grade::get()->where('school_id', auth()->user()->school_id);
        return redirect()->back()->with('message', 'You have successfully delete grade.');
    }

    /**
     * Show the promotion list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function promotionFilter()
    {
        $sessions = Session::where('school_id', auth()->user()->school_id)->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.promotion.promotion', ['sessions' => $sessions, 'classes' => $classes]);
    }

    public function promotionList(Request $request)
    {
        $data = $request->all();
        $promotion_list = Enrollment::where(['session_id' => $data['session_id_from'], 'class_id' => $data['class_id_from'], 'section_id' => $data['section_id_from']])->get();
        echo view('admin.promotion.promotion_list', ['promotion_list' => $promotion_list, 'class_id_to' => $data['class_id_to'], 'section_id_to' => $data['section_id_to'], 'session_id_to' => $data['session_id_to'], 'class_id_from' => $data['class_id_from'], 'section_id_from' => $data['section_id_from']]);
    }

    public function promote($promotion_data = '')
    {
        $promotion_data = explode('-', $promotion_data);
        $enroll_id = $promotion_data[0];
        $class_id = $promotion_data[1];
        $section_id = $promotion_data[2];
        $session_id = $promotion_data[3];

        $enroll = Enrollment::find($enroll_id);

        Enrollment::where('id', $enroll_id)->update([
            'class_id' => $class_id,
            'section_id' => $section_id,
            'session_id' => $session_id,
        ]);

        return true;
    }

    public function classWiseSections($id)
    {
        $sections = Section::get()->where('class_id', $id);
        $options = '<option value="">' . 'Select a section' . '</option>';
        foreach ($sections as $section):
            $options .= '<option value="' . $section->id . '">' . $section->name . '</option>';
        endforeach;
        echo $options;
    }

    /**
     * Show the subject list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function subjectList(Request $request)
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        if (count($request->all()) > 0 && $request->class_id != '') {

            $data = $request->all();
            $class_id = $data['class_id'] ?? '';
            $subjects = Subject::where('class_id', $class_id)->paginate(10);
        } else {
            $subjects = Subject::where('school_id', auth()->user()->school_id)->paginate(10);

            $class_id = '';
        }

        return view('admin.subject.subject_list', compact('subjects', 'classes', 'class_id'));
    }

    public function createSubject()
    {
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.subject.add_subject', ['classes' => $classes]);
    }

    public function subjectCreate(Request $request)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Subject::create([
            'name' => $data['name'],
            'class_id' => $data['class_id'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect('/admin/subject?class_id=' . $data['class_id'])->with('message', 'You have successfully create subject.');
    }

    public function editSubject($id)
    {
        $subject = Subject::find($id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.subject.edit_subject', ['subject' => $subject, 'classes' => $classes]);
    }

    public function subjectUpdate(Request $request, $id)
    {
        $data = $request->all();
        Subject::where('id', $id)->update([
            'name' => $data['name'],
            'class_id' => $data['class_id'],
            'school_id' => auth()->user()->school_id,
        ]);

        return redirect('/admin/subject?class_id=' . $data['class_id'])->with('message', 'You have successfully update subject.');
    }

    public function subjectDelete($id)
    {
        $subject = Subject::find($id);
        $subject->delete();
        $subjects = Subject::get()->where('school_id', auth()->user()->school_id);
        return redirect()->back()->with('message', 'You have successfully delete subject.');
    }

    /**
     * Show the department list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function departmentList(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {

            $departments = Department::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id);
            })->paginate(10);
        } else {
            $departments = Department::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.department.department_list', compact('departments', 'search'));
    }

    public function createDepartment()
    {
        return view('admin.department.add_department');
    }

    public function departmentCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_department_check = Department::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if (count($duplicate_department_check) == 0) {
            Department::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message', 'You have successfully create a new department.');
        } else {
            return back()
                ->with('error', 'Sorry this department already exists');
        }
    }

    public function editDepartment($id)
    {
        $department = Department::find($id);
        return view('admin.department.edit_department', ['department' => $department]);
    }

    public function departmentUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_department_check = Department::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if (count($duplicate_department_check) == 0) {
            Department::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message', 'You have successfully update subject.');
        } else {
            return back()
                ->with('error', 'Sorry this department already exists');
        }
    }

    public function departmentDelete($id)
    {
        $department = Department::find($id);
        $department->delete();
        return redirect()->back()->with('message', 'You have successfully delete department.');
    }


    /**
     * Show the class room list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function classRoomList()
    {
        $class_rooms = ClassRoom::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('admin.class_room.class_room_list', compact('class_rooms'));
    }

    public function createClassRoom()
    {
        return view('admin.class_room.add_class_room');
    }

    public function classRoomCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_class_room_check = ClassRoom::get()->where('name', $data['name']);

        if (count($duplicate_class_room_check) == 0) {
            ClassRoom::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message', 'You have successfully create a new class room.');
        } else {
            return back()
                ->with('error', 'Sorry this class room already exists');
        }
    }

    public function editClassRoom($id)
    {
        $class_room = ClassRoom::find($id);
        return view('admin.class_room.edit_class_room', ['class_room' => $class_room]);
    }

    public function classRoomUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_class_room_check = ClassRoom::get()->where('name', $data['name']);

        if (count($duplicate_class_room_check) == 0) {
            ClassRoom::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message', 'You have successfully update class room.');
        } else {
            return back()
                ->with('error', 'Sorry this class room already exists');
        }
    }

    public function classRoomDelete($id)
    {
        $department = ClassRoom::find($id);
        $department->delete();
        return redirect()->back()->with('message', 'You have successfully delete class room.');
    }

    /**
     * Show the class list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function classList(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {

            $class_lists = Classes::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id);
            })->paginate(10);
        } else {
            $class_lists = Classes::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.class.class_list', compact('class_lists', 'search'));
    }

    public function createClass()
    {
        return view('admin.class.add_class');
    }

    public function classCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_class_check = Classes::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if (count($duplicate_class_check) == 0) {
            $id = Classes::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ])->id;

            Section::create([
                'name' => 'A',
                'class_id' => $id,
            ]);

            return redirect()->back()->with('message', 'You have successfully create a new class.');
        } else {
            return back()
                ->with('error', 'Sorry this class already exists');
        }
    }

    public function editClass($id)
    {
        $class = Classes::find($id);
        return view('admin.class.edit_class', ['class' => $class]);
    }

    public function classUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_class_check = Classes::get()->where('name', $data['name'])->where('school_id', auth()->user()->school_id);

        if (count($duplicate_class_check) == 0) {
            Classes::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
            ]);

            return redirect()->back()->with('message', 'You have successfully update class.');
        } else {
            return back()
                ->with('error', 'Sorry this class already exists');
        }
    }

    public function editSection($id)
    {
        $sections = Section::get()->where('class_id', $id);
        return view('admin.class.sections', ['class_id' => $id, 'sections' => $sections]);
    }

    public function sectionUpdate(Request $request, $id)
    {
        $data = $request->all();

        $section_id = $data['section_id'];
        $section_name = $data['name'];

        foreach ($section_id as $key => $value) {
            if ($value == 0) {
                Section::create([
                    'name' => $section_name[$key],
                    'class_id' => $id,
                ]);
            }
            if ($value != 0 && is_numeric($value)) {
                Section::where(['id' => $value, 'class_id' => $id])->update([
                    'name' => $section_name[$key],
                ]);
            }

            $section_value = null;
            if (strpos($value, 'delete') == true) {
                $section_value = str_replace('delete', '', $value);

                $section = Section::find(['id' => $section_value, 'class_id' => $id]);
                $section->map->delete();
            }
        }

        return redirect()->back()->with('message', 'You have successfully update sections.');
    }

    public function classDelete($id)
    {
        $class = Classes::find($id);
        $class->delete();
        $sections = Section::get()->where('class_id', $id);
        $sections->map->delete();
        $subjects = Subject::get()->where('class_id', $id);
        $subjects->map->delete();
        return redirect()->back()->with('message', 'You have successfully delete class.');
    }

    /**
     * Show the student fee manager.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function studentFeeManagerList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if (count($request->all()) > 0) {
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to  = strtotime($date[1] . ' 23:59:59');
            $selected_class = $data['class'];
            $selected_status = $data['status'];

            if ($selected_class != "all" && $selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_class != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            }


            $classes = Classes::where('school_id', auth()->user()->school_id)->get();

            return view('admin.student_fee_manager.student_fee_manager', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
        } else {
            $classes = Classes::where('school_id', auth()->user()->school_id)->get();
            $date_from = strtotime(date('d-m-Y', strtotime('first day of this month')) . ' 00:00:00');
            $date_to = strtotime(date('d-m-Y', strtotime('last day of this month')) . ' 23:59:59');
            $selected_class = "";
            $selected_status = "";
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            return view('admin.student_fee_manager.student_fee_manager', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
        }
    }

    public function feeManagerExport($date_from = "", $date_to = "", $selected_class = "", $selected_status = "")
    {

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if ($selected_class != "all" && $selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_class != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        }

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();



        $file = "student_fee-" . date('d-m-Y', $date_from) . '-' . date('d-m-Y', $date_to) . '-' . $selected_class . '-' . $selected_status . ".csv";

        $csv_content = get_phrase('Invoice No') . ', ' . get_phrase('Student') . ', ' . get_phrase('Class') . ', ' . get_phrase('Invoice Title') . ', ' . get_phrase('Total Amount') . ', ' . get_phrase('Created At') . ', ' . get_phrase('Paid Amount') . ', ' . get_phrase('Status');

        foreach ($invoices as $invoice) {
            $csv_content .= "\n";

            $student_details = (new CommonController)->get_student_details_by_id($invoice['student_id']);
            $invoice_no = sprintf('%08d', $invoice['id']);

            $csv_content .= $invoice_no . ', ' . $student_details['name'] . ', ' . $student_details['class_name'] . ', ' . $invoice['title'] . ', ' . currency($invoice['total_amount']) . ', ' . date('d-M-Y', $invoice['timestamp']) . ', ' . currency($invoice['paid_amount']) . ', ' . $invoice['status'];
        }
        $txt = fopen($file, "w") or die("Unable to open file!");
        fwrite($txt, $csv_content);
        fclose($txt);

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $file);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        header("Content-type: text/csv");
        readfile($file);
    }


    public function feeManagerExportPdfPrint($date_from = "", $date_to = "", $selected_class = "", $selected_status = "")
    {

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if ($selected_class != "all" && $selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_class != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else if ($selected_status != "all") {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        } else {
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
        }


        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.student_fee_manager.pdf_print', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
    }

    public function createFeeManager($value = "")
    {

        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        if ($value == 'single') {
            return view('admin.student_fee_manager.single', ['classes' => $classes]);
        } else if ($value == 'mass') {
            return view('admin.student_fee_manager.mass', ['classes' => $classes]);
        }
    }

    public function feeManagerCreate(Request $request, $value = "")
    {
        $data = $request->all();
        if ($value == 'single') {

            $baseAmount = (int) ($data['amount'] ?? ($data['total_amount'] ?? 0));
            $discount = (int) ($data['discounted_price'] ?? 0);
            $discount = max(0, min($baseAmount, $discount));
            $total = max(0, $baseAmount - $discount);

            $data['amount'] = $baseAmount;
            $data['discounted_price'] = $discount;
            $data['total_amount'] = $total;

            if ((int) ($data['paid_amount'] ?? 0) > $data['total_amount']) {

                return back()->with('error', 'Paid amount can not get bigger than total amount');
            }
            if ($data['status'] == 'paid' && $data['total_amount'] != $data['paid_amount']) {

                return back()->with('error', 'Paid amount is not equal to total amount');
            }


            $parent_id = User::find($data['student_id'])->toArray();
            $parent_id = $parent_id['parent_id'];
            $data['parent_id'] = $parent_id;
            $data['guardian_id'] = StudentGuardian::where('student_id', $data['student_id'])
                ->orderByDesc('is_fee_payer')
                ->orderByDesc('is_primary')
                ->value('guardian_id');

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            $data['timestamp'] = strtotime(date('d-M-Y'));
            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;


            StudentFeeManager::create($data);

            return redirect()->back()->with('message', 'You have successfully create a new invoice.');
        } else if ($value == 'mass') {

            $total = (int) ($data['total_amount'] ?? 0);
            $data['amount'] = $total;
            $data['discounted_price'] = (int) ($data['discounted_price'] ?? 0);
            $data['discounted_price'] = 0;
            $data['total_amount'] = $total;

            if ((int) ($data['paid_amount'] ?? 0) > $data['total_amount']) {

                return back()->with('error', 'Paid amount can not get bigger than total amount');
            }
            if ($data['status'] == 'paid' && $data['total_amount'] != $data['paid_amount']) {

                return back()->with('error', 'Paid amount is not equal to total amount');
            }

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            $data['timestamp'] = strtotime(date('d-M-Y'));
            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;

            $enrolments = Enrollment::where('class_id', $data['class_id'])
                ->where('section_id', $data['section_id'])
                ->get();



            foreach ($enrolments as $enrolment) {

                $data['student_id'] = $enrolment['user_id'];
                $parent_id = User::find($data['student_id'])->toArray();
                $parent_id = $parent_id['parent_id'];
                $data['parent_id'] = $parent_id;
                $data['guardian_id'] = StudentGuardian::where('student_id', $data['student_id'])
                    ->orderByDesc('is_fee_payer')
                    ->orderByDesc('is_primary')
                    ->value('guardian_id');
                StudentFeeManager::create($data);
            }

            if (sizeof($enrolments) > 0) {

                return redirect()->back()->with('message', 'Invoice added successfully');
            } else {

                return back()->with('error', 'No student found');
            }
        }
    }

    public function classWiseStudents($id = '')
    {
        $enrollments = Enrollment::get()->where('class_id', $id);
        $options = '<option value="">' . 'Select a student' . '</option>';
        foreach ($enrollments as $enrollment):
            $student = User::find($enrollment->user_id);
            $options .= '<option value="' . $student->id . '">' . $student->name . '</option>';
        endforeach;
        echo $options;
    }

    public function classWiseStudentsInvoice($id = '')
    {
        $enrollments = Enrollment::get()->where('section_id', $id);
        $options = '<option value="">' . 'Select a student' . '</option>';
        foreach ($enrollments as $enrollment):
            $student = User::find($enrollment->user_id);
            $options .= '<option value="' . $student->id . '">' . $student->name . '</option>';
        endforeach;
        echo $options;
    }

    public function editFeeManager($id = '')
    {
        $invoice_details = StudentFeeManager::find($id);
        $enrollments = Enrollment::get()->where('class_id', $invoice_details->class_id);
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();
        return view('admin.student_fee_manager.edit', ['invoice_details' => $invoice_details, 'classes' => $classes, 'enrollments' => $enrollments]);
    }

    public function feeManagerUpdate(Request $request, $id = '')
    {
        $data = $request->all();

        /*GET THE PREVIOUS INVOICE DETAILS FOR GETTING THE PAID AMOUNT*/
        $previous_invoice_data = StudentFeeManager::find($id);

        $baseAmount = (int) ($data['amount'] ?? ($data['total_amount'] ?? 0));
        $discount = (int) ($data['discounted_price'] ?? 0);
        $discount = max(0, min($baseAmount, $discount));
        $total = max(0, $baseAmount - $discount);

        $data['amount'] = $baseAmount;
        $data['discounted_price'] = $discount;
        $data['total_amount'] = $total;

        if ((int) ($data['paid_amount'] ?? 0) > $data['total_amount']) {

            return redirect()->back()->with('error', 'Paid amount can not get bigger than total amount');
        }
        if ($data['status'] == 'paid' && $data['total_amount'] != $data['paid_amount']) {
            return redirect()->back()->with('error', 'Paid amount is not equal to total amount');
        }

        /*KEEPING TRACK OF PAYMENT DATE*/
        if ($data['paid_amount'] != $previous_invoice_data['paid_amount'] && $data['paid_amount'] == $previous_invoice_data['total_amount']) {
            $timestamp = strtotime(date('d-M-Y'));
        } else {
            $timestamp = $previous_invoice_data['timestamp'];
        }

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        StudentFeeManager::where('id', $id)->update([
            'title' => $data['title'],
            'total_amount' => $data['total_amount'],
            'class_id' => $data['class_id'],
            'student_id' => $data['student_id'],
            'amount' => $data['amount'],
            'discounted_price' => $data['discounted_price'],
            'paid_amount' => $data['paid_amount'],
            'payment_method' => $data['payment_method'],
            'timestamp' => $timestamp,
            'status' => $data['status'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message', 'You have successfully update invoice.');
    }

    public function studentFeeDelete($id)
    {
        $invoice = StudentFeeManager::find($id);
        $invoice->delete();
        return redirect()->back()->with('message', 'You have successfully delete invoice.');
    }

    /**
     * Fee setup: Class fees (base amount per class/section for current session).
     */
    public function classFees()
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');

        $classes = Classes::where('school_id', $schoolId)->get();
        $fees = ClassFeeStructure::where('school_id', $schoolId)
            ->where('session_id', $activeSession)
            ->orderByDesc('id')
            ->get();

        return view('admin.fees.class_fees', compact('classes', 'fees', 'activeSession'));
    }

    public function classFeesStore(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');

        $data = $request->validate([
            'class_id' => ['required', 'integer'],
            'section_id' => ['nullable', 'integer'],
            'title' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'integer', 'min:0'],
        ]);

        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            $title = 'Monthly Fee';
        }

        ClassFeeStructure::updateOrCreate(
            [
                'school_id' => $schoolId,
                'session_id' => $activeSession,
                'class_id' => (int) $data['class_id'],
                'section_id' => !empty($data['section_id']) ? (int) $data['section_id'] : null,
            ],
            [
                'title' => $title,
                'amount' => (int) $data['amount'],
            ]
        );

        return redirect()->back()->with('message', 'Class fee saved successfully.');
    }

    /**
     * Fee setup: Scholarships/discounts (concessions) for students or families (guardian).
     */
    public function feeConcessions()
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');
        $classes = Classes::where('school_id', $schoolId)->get();

        $concessions = FeeConcession::where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $studentIds = $concessions->pluck('student_id')->filter()->unique()->values()->all();
        $guardianIds = $concessions->pluck('guardian_id')->filter()->unique()->values()->all();

        $studentsById = collect();
        if (!empty($studentIds)) {
            $studentsById = \App\Models\User::whereIn('id', $studentIds)
                ->select(['id', 'name'])
                ->get()
                ->keyBy('id');
        }

        $guardiansById = collect();
        if (!empty($guardianIds)) {
            $guardiansById = \App\Models\Guardian::where('school_id', $schoolId)
                ->whereIn('id', $guardianIds)
                ->select(['id', 'name', 'id_card_no'])
                ->get()
                ->keyBy('id');
        }

        // Latest known class/section per student (fallback if concessions are session-specific).
        $enrollmentByStudentId = [];
        if (!empty($studentIds)) {
            $enrollments = \Illuminate\Support\Facades\DB::table('enrollments as e')
                ->leftJoin('classes as c', 'c.id', '=', 'e.class_id')
                ->leftJoin('sections as sec', 'sec.id', '=', 'e.section_id')
                ->where('e.school_id', $schoolId)
                ->whereIn('e.user_id', $studentIds)
                ->orderByDesc('e.id')
                ->select([
                    'e.user_id',
                    'c.name as class_name',
                    'sec.name as section_name',
                ])
                ->get();

            foreach ($enrollments as $row) {
                $sid = (int) ($row->user_id ?? 0);
                if ($sid <= 0 || isset($enrollmentByStudentId[$sid])) {
                    continue;
                }
                $enrollmentByStudentId[$sid] = [
                    'class_name' => (string) ($row->class_name ?? ''),
                    'section_name' => (string) ($row->section_name ?? ''),
                ];
            }
        }

        return view('admin.fees.concessions', compact('classes', 'concessions', 'activeSession', 'studentsById', 'guardiansById', 'enrollmentByStudentId'));
    }

    public function feeConcessionsStore(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');

        $data = $request->validate([
            'scope_type' => ['required', 'string'], // student|guardian
            'student_id' => ['nullable', 'integer'],
            'guardian_id' => ['nullable', 'integer'],
            'guardian_cnic' => ['nullable', 'string', 'max:30'],
            'mode' => ['required', 'string'], // percent|fixed
            'value' => ['required', 'integer', 'min:0'],
            'session_id' => ['nullable', 'integer'],
            'note' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable'],
        ]);

        $scope = strtolower(trim((string) $data['scope_type']));
        $mode = strtolower(trim((string) $data['mode']));
        if (!in_array($scope, ['student', 'guardian'], true)) {
            return redirect()->back()->with('error', 'Invalid scope type.');
        }
        if (!in_array($mode, ['percent', 'fixed'], true)) {
            return redirect()->back()->with('error', 'Invalid discount mode.');
        }

        $studentId = null;
        $guardianId = null;

        if ($scope === 'student') {
            $studentId = (int) ($data['student_id'] ?? 0);
            if ($studentId <= 0) {
                return redirect()->back()->with('error', 'Please select a student.');
            }

            $guardianId = StudentGuardian::where('student_id', $studentId)
                ->orderByDesc('is_fee_payer')
                ->orderByDesc('is_primary')
                ->value('guardian_id');
        } else {
            $guardianId = !empty($data['guardian_id']) ? (int) $data['guardian_id'] : null;
            if (empty($guardianId)) {
                $cnic = normalize_id_card_no((string) ($data['guardian_cnic'] ?? ''));
                if ($cnic === '' || strlen($cnic) !== 13) {
                    return redirect()->back()->with('error', 'Please enter a valid 13-digit Father CNIC.');
                }
                $guardianId = Guardian::where('school_id', $schoolId)
                    ->where('id_card_no_normalized', $cnic)
                    ->value('id');
            } else {
                $guardianId = Guardian::where('school_id', $schoolId)->where('id', $guardianId)->value('id');
            }
            if (empty($guardianId)) {
                return redirect()->back()->with('error', 'Guardian not found for this CNIC.');
            }
        }

        if ($mode === 'percent' && (int) $data['value'] > 100) {
            return redirect()->back()->with('error', 'Percent discount cannot exceed 100.');
        }

        FeeConcession::create([
            'school_id' => $schoolId,
            'session_id' => !empty($data['session_id']) ? (int) $data['session_id'] : null,
            'scope_type' => $scope,
            'student_id' => $studentId,
            'guardian_id' => $guardianId,
            'mode' => $mode,
            'value' => (int) $data['value'],
            'is_active' => !empty($request->input('is_active')) ? 1 : 0,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->back()->with('message', 'Discount/Scholarship saved successfully.');
    }

    /**
     * Fee setup: sibling discounts (youngest child logic).
     */
    public function feeSiblingDiscounts()
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');

        $rules = FeeSiblingDiscount::where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $guardianIds = $rules->pluck('guardian_id')->filter()->unique()->values()->all();
        $guardiansById = collect();
        if (!empty($guardianIds)) {
            $guardiansById = \App\Models\Guardian::where('school_id', $schoolId)
                ->whereIn('id', $guardianIds)
                ->select(['id', 'name', 'id_card_no'])
                ->get()
                ->keyBy('id');
        }

        return view('admin.fees.sibling_discounts', compact('rules', 'activeSession', 'guardiansById'));
    }

    public function feeSiblingDiscountsStore(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');

        $data = $request->validate([
            'basis' => ['required', 'string'], // dob|class|hybrid
            'min_children' => ['required', 'integer', 'min:1', 'max:10'],
            'mode' => ['required', 'string'], // percent|fixed
            'value' => ['required', 'integer', 'min:0'],
            'session_id' => ['nullable', 'integer'],
            'guardian_id' => ['nullable', 'integer'],
            'guardian_cnic' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable'],
        ]);

        $basis = strtolower(trim((string) $data['basis']));
        if (!in_array($basis, ['dob', 'class', 'hybrid'], true)) {
            return redirect()->back()->with('error', 'Invalid basis.');
        }

        $mode = strtolower(trim((string) $data['mode']));
        if (!in_array($mode, ['percent', 'fixed'], true)) {
            return redirect()->back()->with('error', 'Invalid mode.');
        }

        if ($mode === 'percent' && (int) $data['value'] > 100) {
            return redirect()->back()->with('error', 'Percent discount cannot exceed 100.');
        }

        $guardianId = !empty($data['guardian_id']) ? (int) $data['guardian_id'] : null;
        if (!empty($guardianId)) {
            $guardianId = Guardian::where('school_id', $schoolId)->where('id', $guardianId)->value('id');
            if (empty($guardianId)) {
                return redirect()->back()->with('error', 'Guardian not found.');
            }
        } else {
            $cnic = normalize_id_card_no((string) ($data['guardian_cnic'] ?? ''));
            if ($cnic !== '') {
                if (strlen($cnic) !== 13) {
                    return redirect()->back()->with('error', 'Father CNIC must be 13 digits.');
                }
                $guardianId = Guardian::where('school_id', $schoolId)
                    ->where('id_card_no_normalized', $cnic)
                    ->value('id');
                if (empty($guardianId)) {
                    return redirect()->back()->with('error', 'Guardian not found for this CNIC.');
                }
            }
        }

        FeeSiblingDiscount::create([
            'school_id' => $schoolId,
            'session_id' => !empty($data['session_id']) ? (int) $data['session_id'] : null,
            'guardian_id' => $guardianId,
            'basis' => $basis,
            'min_children' => (int) $data['min_children'],
            'mode' => $mode,
            'value' => (int) $data['value'],
            'is_active' => !empty($request->input('is_active')) ? 1 : 0,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->back()->with('message', 'Sibling discount rule saved successfully.');
    }

    /**
     * Select2 search: students (name/email/father/CNIC) with class/section.
     */
    public function feeSearchStudents(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;
        $q = trim((string) $request->get('q', ''));
        $limit = 20;

        $query = DB::table('enrollments as e')
            ->join('users as s', 's.id', '=', 'e.user_id')
            ->leftJoin('classes as c', 'c.id', '=', 'e.class_id')
            ->leftJoin('sections as sec', 'sec.id', '=', 'e.section_id')
            ->leftJoin('student_guardians as sg', function ($join) {
                $join->on('sg.student_id', '=', 's.id')
                    ->where('sg.relation', '=', 'father')
                    ->where('sg.is_primary', '=', 1);
            })
            ->leftJoin('guardians as g', 'g.id', '=', 'sg.guardian_id')
            ->where('s.school_id', $schoolId)
            ->where('s.role_id', 7);

        if ($q !== '') {
            $digits = normalize_id_card_no($q);
            $query->where(function ($w) use ($q, $digits) {
                $w->where('s.name', 'LIKE', "%{$q}%")
                    ->orWhere('s.email', 'LIKE', "%{$q}%")
                    ->orWhere('c.name', 'LIKE', "%{$q}%")
                    ->orWhere('sec.name', 'LIKE', "%{$q}%")
                    ->orWhere('g.name', 'LIKE', "%{$q}%")
                    ->orWhere('g.id_card_no', 'LIKE', "%{$q}%");
                if ($digits !== '') {
                    $w->orWhere('g.id_card_no_normalized', 'LIKE', "%{$digits}%");
                }
            });
        }

        $rows = $query->select([
            's.id as student_id',
            's.name as student_name',
            'c.name as class_name',
            'sec.name as section_name',
            'g.name as father_name',
            'g.id_card_no as father_cnic',
        ])->orderBy('s.name')->limit($limit)->get();

        $results = [];
        foreach ($rows as $r) {
            $text = trim(($r->student_name ?? '') . ' — ' . ($r->class_name ?? '') . ' ' . ($r->section_name ?? ''));
            $father = trim((string) ($r->father_name ?? ''));
            $cnic = trim((string) ($r->father_cnic ?? ''));
            if ($father !== '' || $cnic !== '') {
                $text .= ' | ' . ($father !== '' ? $father : 'Father') . ($cnic !== '' ? (' (' . $cnic . ')') : '');
            }
            $results[] = ['id' => (int) $r->student_id, 'text' => $text];
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Select2 search: guardians (father) by name/CNIC.
     */
    public function feeSearchGuardians(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;
        $q = trim((string) $request->get('q', ''));
        $limit = 20;

        $query = Guardian::where('school_id', $schoolId);
        if ($q !== '') {
            $digits = normalize_id_card_no($q);
            $query->where(function ($w) use ($q, $digits) {
                $w->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('id_card_no', 'LIKE', "%{$q}%");
                if ($digits !== '') {
                    $w->orWhere('id_card_no_normalized', 'LIKE', "%{$digits}%");
                }
            });
        }

        $guardians = $query->orderBy('name')->limit($limit)->get();
        $results = [];
        foreach ($guardians as $g) {
            $childCount = StudentGuardian::where('guardian_id', $g->id)->distinct()->count('student_id');
            $text = trim(($g->name ?? '') . ' — ' . ($g->id_card_no ?? ''));
            $text .= ' | children: ' . $childCount;
            $results[] = ['id' => (int) $g->id, 'text' => $text];
        }

        return response()->json(['results' => $results]);
    }

    /**
     * Parent applications (Leave / Other): admin review list.
     */
    public function applications(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;

        $status = strtolower(trim((string) $request->get('status', 'pending')));
        $type = strtolower(trim((string) $request->get('type', '')));
        $q = trim((string) $request->get('q', ''));

        $query = DB::table('school_applications as a')
            ->leftJoin('users as p', 'p.id', '=', 'a.parent_id')
            ->leftJoin('users as s', 's.id', '=', 'a.student_id')
            ->leftJoin('classes as c', 'c.id', '=', 'a.class_id')
            ->leftJoin('sections as sec', 'sec.id', '=', 'a.section_id')
            ->leftJoin('guardians as g', 'g.id', '=', 'a.guardian_id')
            ->where('a.school_id', $schoolId);

        if ($status !== '' && $status !== 'all') {
            $query->where('a.status', $status);
        }
        if ($type !== '') {
            $query->where('a.type', $type);
        }
        if ($q !== '') {
            $digits = normalize_id_card_no($q);
            $query->where(function ($w) use ($q, $digits) {
                $w->where('a.title', 'LIKE', "%{$q}%")
                    ->orWhere('a.message', 'LIKE', "%{$q}%")
                    ->orWhere('p.name', 'LIKE', "%{$q}%")
                    ->orWhere('s.name', 'LIKE', "%{$q}%")
                    ->orWhere('g.name', 'LIKE', "%{$q}%")
                    ->orWhere('g.id_card_no', 'LIKE', "%{$q}%");
                if ($digits !== '') {
                    $w->orWhere('g.id_card_no_normalized', 'LIKE', "%{$digits}%");
                }
            });
        }

        $rows = $query->select([
            'a.*',
            'p.name as parent_name',
            's.name as student_name',
            'c.name as class_name',
            'sec.name as section_name',
            'g.name as guardian_name',
            'g.id_card_no as guardian_cnic',
        ])->orderByDesc('a.id')->paginate(30)->appends($request->query());

        return view('admin.applications.index', compact('rows', 'status', 'type', 'q'));
    }

    public function applicationsDecision(Request $request, int $id)
    {
        $schoolId = (int) auth()->user()->school_id;

        $data = $request->validate([
            'decision' => ['required', 'string', 'in:approved,rejected'],
            'decision_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $app = SchoolApplication::where('school_id', $schoolId)->where('id', $id)->first();
        if (empty($app)) {
            return redirect()->back()->with('error', 'Application not found.');
        }

        if (!in_array((string) $app->status, ['pending', 'approved', 'rejected'], true)) {
            $app->status = 'pending';
        }

        $app->status = (string) $data['decision'];
        $app->decision_note = !empty($data['decision_note']) ? trim((string) $data['decision_note']) : null;
        $app->decided_by = (int) auth()->user()->id;
        $app->decided_at = now();
        $app->save();

        // Email notification to parent (if SMTP configured)
        try {
            $parent = \App\Models\User::find((int) $app->parent_id);
            $parentEmail = !empty($parent) ? trim((string) $parent->email) : '';
            if (
                $parentEmail !== '' &&
                !empty(get_settings('smtp_user')) &&
                !empty(get_settings('smtp_pass')) &&
                !empty(get_settings('smtp_host')) &&
                !empty(get_settings('smtp_port'))
            ) {
                Mail::to($parentEmail)->send(new ApplicationStatusEmail($app, (string) ($parent->name ?? 'Parent')));
            }
        } catch (\Throwable $e) {
            Log::error('Application decision email failed', [
                'application_id' => (int) $app->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->back()->with('message', 'Application updated successfully.');
    }

    /**
     * Fee generator wizard.
     */
    public function feeGenerator()
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');
        $classes = Classes::where('school_id', $schoolId)->get();

        $generatedFeeGroupId = (string) request()->get('fee_group_id', '');
        $generatedGuardians = [];
        if ($generatedFeeGroupId !== '') {
            $generatedGuardians = StudentFeeManager::where('school_id', $schoolId)
                ->where('session_id', $activeSession)
                ->where('fee_group_id', $generatedFeeGroupId)
                ->whereNotNull('guardian_id')
                ->distinct()
                ->pluck('guardian_id')
                ->toArray();
        }

        return view('admin.fees.generator', compact('classes', 'activeSession', 'generatedFeeGroupId', 'generatedGuardians'));
    }

    public function feeGeneratorPreview(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');

        $data = $request->validate([
            'class_id' => ['required', 'integer'],
            'section_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:100'],
            'billing_month' => ['required', 'integer', 'min:1', 'max:12'],
            'billing_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'due_date' => ['nullable', 'date'],
            'pool_by_guardian' => ['nullable'],
        ]);

        $classId = (int) $data['class_id'];
        $sectionId = (int) $data['section_id'];
        $pool = !empty($request->input('pool_by_guardian')) ? 1 : 0;

        $classFee = ClassFeeStructure::where('school_id', $schoolId)
            ->where('session_id', $activeSession)
            ->where('class_id', $classId)
            ->where(function ($q) use ($sectionId) {
                $q->whereNull('section_id')->orWhere('section_id', $sectionId);
            })
            ->orderByRaw('section_id is null') // prefer section-specific
            ->first();

        $baseAmount = (int) ($classFee->amount ?? 0);

        $enrolments = Enrollment::where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->get();

        // Preload active sibling discount rules (guardian-specific overrides global)
        $activeSiblingRules = FeeSiblingDiscount::where('school_id', $schoolId)
            ->where('is_active', 1)
            ->where(function ($q) use ($activeSession) {
                $q->whereNull('session_id')->orWhere('session_id', $activeSession);
            })
            ->orderByRaw('guardian_id is null') // prefer guardian-specific (not null)
            ->orderByDesc('id')
            ->get();

        $rows = [];
        $byGuardian = [];
        foreach ($enrolments as $enrol) {
            $studentId = (int) $enrol->user_id;
            $student = User::find($studentId);
            if (empty($student)) {
                continue;
            }

            $guardianId = StudentGuardian::where('student_id', $studentId)
                ->orderByDesc('is_fee_payer')
                ->orderByDesc('is_primary')
                ->value('guardian_id');

            $guardian = !empty($guardianId) ? Guardian::find($guardianId) : null;

            // concession lookup: student first, then guardian
            $concession = FeeConcession::where('school_id', $schoolId)
                ->where('is_active', 1)
                ->where(function ($q) use ($activeSession) {
                    $q->whereNull('session_id')->orWhere('session_id', $activeSession);
                })
                ->where(function ($q) use ($studentId, $guardianId) {
                    $q->where(function ($qq) use ($studentId) {
                        $qq->where('scope_type', 'student')->where('student_id', $studentId);
                    });
                    if (!empty($guardianId)) {
                        $q->orWhere(function ($qq) use ($guardianId) {
                            $qq->where('scope_type', 'guardian')->where('guardian_id', $guardianId);
                        });
                    }
                })
                ->orderByRaw("scope_type = 'student' desc")
                ->orderByDesc('id')
                ->first();

            $discount = 0;
            if (!empty($concession) && $baseAmount > 0) {
                if ($concession->mode === 'percent') {
                    $p = max(0, min(100, (int) $concession->value));
                    $discount = (int) floor(($baseAmount * $p) / 100);
                } else {
                    $discount = max(0, min($baseAmount, (int) $concession->value));
                }
            }

            $payable = max(0, $baseAmount - $discount);

            $row = [
                'student_id' => $studentId,
                'student_name' => $student->name ?? '',
                'guardian_id' => $guardianId,
                'guardian_name' => $guardian->name ?? '',
                'guardian_cnic' => $guardian->id_card_no ?? '',
                'base_amount' => $baseAmount,
                'discount_amount' => $discount,
                'total_amount' => $payable,
                'discount_reason' => !empty($concession) ? (($concession->scope_type ?? 'concession') . ' ' . ($concession->mode ?? '') . ' ' . ($concession->value ?? '')) : '',
            ];
            $rows[] = $row;

            if (!empty($guardianId)) {
                $byGuardian[(string) $guardianId][] = count($rows) - 1; // row index
            }
        }

        // Apply sibling discount rules (youngest child only)
        foreach ($byGuardian as $gidStr => $rowIndexes) {
            $gid = (int) $gidStr;

            $rule = $activeSiblingRules->firstWhere('guardian_id', $gid) ?? $activeSiblingRules->firstWhere('guardian_id', null);
            if (empty($rule)) {
                continue;
            }

            $resolved = $this->resolveYoungestStudentForGuardian($schoolId, $activeSession, $gid, (string) $rule->basis);
            $siblingCount = (int) ($resolved['count'] ?? 0);
            $youngestId = (int) ($resolved['youngest_student_id'] ?? 0);

            if ($siblingCount < (int) $rule->min_children || $youngestId <= 0) {
                continue;
            }

            foreach ($rowIndexes as $idx) {
                if ((int) ($rows[$idx]['student_id'] ?? 0) !== $youngestId) {
                    continue;
                }

                $base = (int) ($rows[$idx]['base_amount'] ?? 0);
                if ($base <= 0) {
                    continue;
                }

                $sibDiscount = 0;
                if ($rule->mode === 'percent') {
                    $p = max(0, min(100, (int) $rule->value));
                    $sibDiscount = (int) floor(($base * $p) / 100);
                } else {
                    $sibDiscount = max(0, min($base, (int) $rule->value));
                }

                // Choose the higher discount (no stacking) to avoid accidental over-discounting.
                $finalDiscount = max((int) ($rows[$idx]['discount_amount'] ?? 0), $sibDiscount);
                $finalDiscount = max(0, min($base, $finalDiscount));

                $rows[$idx]['discount_amount'] = $finalDiscount;
                $rows[$idx]['total_amount'] = max(0, $base - $finalDiscount);
                $rows[$idx]['discount_reason'] = 'sibling:' . ($rule->basis ?? 'hybrid') . ' youngest (' . ($rule->mode ?? '') . ' ' . ($rule->value ?? '') . ')';
            }
        }

        $classes = Classes::where('school_id', $schoolId)->get();
        $preview = [
            'fee_title' => $classFee->title ?? 'Monthly Fee',
            'base_amount' => $baseAmount,
            'pool' => $pool,
            'rows' => $rows,
        ];

        return view('admin.fees.generator', compact('classes', 'activeSession', 'preview', 'data'));
    }

    public function feeGeneratorGenerate(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');

        $data = $request->validate([
            'class_id' => ['required', 'integer'],
            'section_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:100'],
            'billing_month' => ['required', 'integer', 'min:1', 'max:12'],
            'billing_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'due_date' => ['nullable', 'date'],
            'pool_by_guardian' => ['nullable'],
        ]);

        $classId = (int) $data['class_id'];
        $sectionId = (int) $data['section_id'];
        $pool = !empty($request->input('pool_by_guardian')) ? 1 : 0;
        $feeGroupId = (string) \Illuminate\Support\Str::uuid();

        $classFee = ClassFeeStructure::where('school_id', $schoolId)
            ->where('session_id', $activeSession)
            ->where('class_id', $classId)
            ->where(function ($q) use ($sectionId) {
                $q->whereNull('section_id')->orWhere('section_id', $sectionId);
            })
            ->orderByRaw('section_id is null')
            ->first();

        $baseAmount = (int) ($classFee->amount ?? 0);

        $enrolments = Enrollment::where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->get();

        $activeSiblingRules = FeeSiblingDiscount::where('school_id', $schoolId)
            ->where('is_active', 1)
            ->where(function ($q) use ($activeSession) {
                $q->whereNull('session_id')->orWhere('session_id', $activeSession);
            })
            ->orderByRaw('guardian_id is null')
            ->orderByDesc('id')
            ->get();

        // Pre-resolve youngest per guardian for this class/section run (so we don't recompute for every student).
        $guardianIdsInRun = [];
        foreach ($enrolments as $enrol) {
            $sid = (int) $enrol->user_id;
            $gid = (int) (StudentGuardian::where('student_id', $sid)
                ->orderByDesc('is_fee_payer')
                ->orderByDesc('is_primary')
                ->value('guardian_id') ?? 0);
            if ($gid > 0) {
                $guardianIdsInRun[(string) $gid] = $gid;
            }
        }

        $youngestMap = []; // guardian_id => ['youngest_student_id'=>..., 'count'=>..., 'rule'=>FeeSiblingDiscount|null]
        foreach ($guardianIdsInRun as $gid) {
            $rule = $activeSiblingRules->firstWhere('guardian_id', $gid) ?? $activeSiblingRules->firstWhere('guardian_id', null);
            if (empty($rule)) {
                continue;
            }
            $resolved = $this->resolveYoungestStudentForGuardian($schoolId, $activeSession, (int) $gid, (string) $rule->basis);
            $youngestMap[(string) $gid] = [
                'youngest_student_id' => (int) ($resolved['youngest_student_id'] ?? 0),
                'count' => (int) ($resolved['count'] ?? 0),
                'rule_id' => (int) ($rule->id ?? 0),
                'basis' => (string) ($rule->basis ?? 'hybrid'),
                'mode' => (string) ($rule->mode ?? 'percent'),
                'value' => (int) ($rule->value ?? 0),
                'min_children' => (int) ($rule->min_children ?? 2),
            ];
        }

        $created = 0;
        $guardiansInGroup = [];

        foreach ($enrolments as $enrol) {
            $studentId = (int) $enrol->user_id;
            $student = User::find($studentId);
            if (empty($student)) {
                continue;
            }

            $parentId = (int) ($student->parent_id ?? 0);

            $guardianId = StudentGuardian::where('student_id', $studentId)
                ->orderByDesc('is_fee_payer')
                ->orderByDesc('is_primary')
                ->value('guardian_id');

            $concession = FeeConcession::where('school_id', $schoolId)
                ->where('is_active', 1)
                ->where(function ($q) use ($activeSession) {
                    $q->whereNull('session_id')->orWhere('session_id', $activeSession);
                })
                ->where(function ($q) use ($studentId, $guardianId) {
                    $q->where(function ($qq) use ($studentId) {
                        $qq->where('scope_type', 'student')->where('student_id', $studentId);
                    });
                    if (!empty($guardianId)) {
                        $q->orWhere(function ($qq) use ($guardianId) {
                            $qq->where('scope_type', 'guardian')->where('guardian_id', $guardianId);
                        });
                    }
                })
                ->orderByRaw("scope_type = 'student' desc")
                ->orderByDesc('id')
                ->first();

            $discount = 0;
            if (!empty($concession) && $baseAmount > 0) {
                if ($concession->mode === 'percent') {
                    $p = max(0, min(100, (int) $concession->value));
                    $discount = (int) floor(($baseAmount * $p) / 100);
                } else {
                    $discount = max(0, min($baseAmount, (int) $concession->value));
                }
            }

            $discountReason = !empty($concession) ? 'concession:' . ($concession->scope_type ?? '') : '';

            // Sibling discount (youngest child)
            if (!empty($guardianId) && isset($youngestMap[(string) $guardianId])) {
                $m = $youngestMap[(string) $guardianId];
                if (($m['count'] ?? 0) >= ($m['min_children'] ?? 2) && (int) ($m['youngest_student_id'] ?? 0) === $studentId) {
                    $sibDiscount = 0;
                    if (($m['mode'] ?? '') === 'percent') {
                        $p = max(0, min(100, (int) ($m['value'] ?? 0)));
                        $sibDiscount = (int) floor(($baseAmount * $p) / 100);
                    } else {
                        $sibDiscount = max(0, min($baseAmount, (int) ($m['value'] ?? 0)));
                    }

                    $finalDiscount = max($discount, $sibDiscount);
                    $finalDiscount = max(0, min($baseAmount, $finalDiscount));
                    if ($finalDiscount !== $discount) {
                        $discount = $finalDiscount;
                        $discountReason = 'sibling:' . ($m['basis'] ?? 'hybrid') . ' youngest';
                    }
                }
            }

            $payable = max(0, $baseAmount - $discount);

            StudentFeeManager::create([
                'title' => $data['title'],
                'amount' => $baseAmount,
                'discounted_price' => $discount,
                'total_amount' => $payable,
                'class_id' => $classId,
                'parent_id' => $parentId ?: null,
                'guardian_id' => $guardianId ?: null,
                'student_id' => $studentId,
                'payment_method' => 'cash',
                'paid_amount' => 0,
                'status' => 'unpaid',
                'school_id' => $schoolId,
                'session_id' => $activeSession,
                'timestamp' => strtotime(date('d-M-Y')),
                'fee_group_id' => $feeGroupId,
                'billing_month' => (int) $data['billing_month'],
                'billing_year' => (int) $data['billing_year'],
                'due_date' => $data['due_date'] ?? null,
                'fee_breakdown' => json_encode([
                    'base_amount' => $baseAmount,
                    'discount_amount' => $discount,
                    'pool_by_guardian' => $pool,
                    'discount_reason' => $discountReason,
                ]),
            ]);

            $created++;
            if (!empty($guardianId)) {
                $guardiansInGroup[(string) $guardianId] = (int) $guardianId;
            }
        }

        if ($created <= 0) {
            return redirect()->back()->with('error', 'No student found.');
        }

        // Redirect back to generator; the group id can be used for family receipt printing.
        return redirect()->route('admin.fees.generator', [
            'generated' => 1,
            'fee_group_id' => $feeGroupId,
        ])->with('message', 'Invoices generated successfully.');
    }

    /**
     * Print a family receipt for a generator run + guardian.
     */
    public function feeFamilyReceipt($fee_group_id, $guardian_id)
    {
        $schoolId = (int) auth()->user()->school_id;
        $activeSession = (int) get_school_settings($schoolId)->value('running_session');

        $feeGroupId = (string) $fee_group_id;
        $guardianId = (int) $guardian_id;

        $guardian = Guardian::where('school_id', $schoolId)->where('id', $guardianId)->first();

        $invoices = StudentFeeManager::where('school_id', $schoolId)
            ->where('session_id', $activeSession)
            ->where('fee_group_id', $feeGroupId)
            ->where('guardian_id', $guardianId)
            ->orderBy('student_id')
            ->get();

        $students = [];
        foreach ($invoices as $inv) {
            $students[] = (new CommonController)->get_student_details_by_id($inv->student_id);
        }

        return view('admin.fees.family_receipt', compact('guardian', 'invoices', 'students', 'feeGroupId'));
    }

    /**
     * Resolve youngest student in a family for sibling discount rules.
     * Returns: ['youngest_student_id' => int, 'count' => int]
     */
    private function resolveYoungestStudentForGuardian(int $schoolId, int $activeSession, int $guardianId, string $basis): array
    {
        $basis = strtolower(trim($basis));
        if (!in_array($basis, ['dob', 'class', 'hybrid'], true)) {
            $basis = 'hybrid';
        }

        $studentIds = StudentGuardian::where('guardian_id', $guardianId)->pluck('student_id')->toArray();
        if (empty($studentIds)) {
            return ['youngest_student_id' => 0, 'count' => 0];
        }

        $students = User::whereIn('id', $studentIds)
            ->where('school_id', $schoolId)
            ->where('role_id', 7)
            ->get();

        $rows = [];
        foreach ($students as $s) {
            $info = json_decode((string) ($s->user_information ?? ''), true);
            $birthday = null;
            if (is_array($info) && isset($info['birthday']) && is_numeric($info['birthday'])) {
                $birthday = (int) $info['birthday'];
            }

            $enrol = Enrollment::where('user_id', $s->id)
                ->where('school_id', $schoolId)
                ->where(function ($q) use ($activeSession) {
                    $q->where('session_id', $activeSession)->orWhereNull('session_id');
                })
                ->first();
            if (empty($enrol)) {
                $enrol = Enrollment::where('user_id', $s->id)->where('school_id', $schoolId)->first();
            }

            $classId = (int) ($enrol->class_id ?? 0);
            $class = $classId > 0 ? Classes::find($classId) : null;
            $className = $class->name ?? '';
            $classOrder = $class->sort_order ?? null;
            if ($classOrder === null) {
                // Fallback: parse numeric grade from class name (works for "Class 1", "Grade 2", etc.)
                $classOrder = 9999;
                if (preg_match('/(\d{1,2})/', (string) $className, $m)) {
                    $classOrder = (int) $m[1];
                } else {
                    $n = strtolower((string) $className);
                    $map = [
                        'playgroup' => 0,
                        'play group' => 0,
                        'nursery' => 1,
                        'prep' => 2,
                        'kg' => 2,
                        'k.g' => 2,
                        'montessori' => 1,
                    ];
                    foreach ($map as $key => $val) {
                        if (str_contains($n, $key)) {
                            $classOrder = $val;
                            break;
                        }
                    }
                }
            }

            $rows[] = [
                'student_id' => (int) $s->id,
                'birthday' => $birthday,
                'class_order' => (int) $classOrder,
            ];
        }

        $count = count($rows);
        if ($count <= 0) {
            return ['youngest_student_id' => 0, 'count' => 0];
        }

        $allHaveDob = true;
        foreach ($rows as $r) {
            if (empty($r['birthday'])) {
                $allHaveDob = false;
                break;
            }
        }

        $useDob = ($basis === 'dob') || ($basis === 'hybrid' && $allHaveDob);

        usort($rows, function ($a, $b) use ($useDob) {
            if ($useDob) {
                // Younger = larger birthday timestamp
                $cmp = (int) ($b['birthday'] ?? 0) <=> (int) ($a['birthday'] ?? 0);
                if ($cmp !== 0) return $cmp;
                // tie-break: smaller class first (younger grade)
                $cmp2 = (int) ($a['class_order'] ?? 9999) <=> (int) ($b['class_order'] ?? 9999);
                if ($cmp2 !== 0) return $cmp2;
                return (int) ($b['student_id'] ?? 0) <=> (int) ($a['student_id'] ?? 0);
            }

            // Younger by class = smaller class_order
            $cmp = (int) ($a['class_order'] ?? 9999) <=> (int) ($b['class_order'] ?? 9999);
            if ($cmp !== 0) return $cmp;
            // tie-break by DOB if available (younger DOB => larger ts)
            $cmp2 = (int) ($b['birthday'] ?? 0) <=> (int) ($a['birthday'] ?? 0);
            if ($cmp2 !== 0) return $cmp2;
            return (int) ($b['student_id'] ?? 0) <=> (int) ($a['student_id'] ?? 0);
        });

        $youngest = $rows[0] ?? null;
        return [
            'youngest_student_id' => (int) ($youngest['student_id'] ?? 0),
            'count' => $count,
        ];
    }

    /**
     * Show the expense expense list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function expenseList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        if (count($request->all()) > 0) {
            $data = $request->all();

            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to  = strtotime($date[1] . ' 23:59:59');
            $expense_category_id = $data['expense_category_id'];

            $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
            $selected_category = ExpenseCategory::find($expense_category_id);
            if ($expense_category_id != 'all') {
                $expenses = Expense::where('expense_category_id', $expense_category_id)
                    ->where('date', '>=', $date_from)
                    ->where('date', '<=', $date_to)
                    ->where('school_id', auth()->user()->school_id)
                    ->where('session_id', $active_session)
                    ->get();
            } else {
                $expenses = Expense::where('date', '>=', $date_from)
                    ->where('date', '<=', $date_to)
                    ->where('school_id', auth()->user()->school_id)
                    ->where('session_id', $active_session)
                    ->get();
            }

            return view('admin.expenses.expense_manager', ['expense_categories' => $expense_categories, 'expenses' => $expenses, 'selected_category' => $selected_category, 'date_from' => $date_from, 'date_to' => $date_to]);
        } else {
            $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
            $selected_category = "";
            $date_from = strtotime(date('d-m-Y', strtotime('first day of this month')) . ' 00:00:00');
            $date_to = strtotime(date('d-m-Y', strtotime('last day of this month')) . ' 23:59:59');
            $expenses = Expense::where('date', '>=', $date_from)
                ->where('date', '<=', $date_to)
                ->where('school_id', auth()->user()->school_id)
                ->where('session_id', $active_session)
                ->get();
            return view('admin.expenses.expense_manager', ['expense_categories' => $expense_categories, 'expenses' => $expenses, 'selected_category' => $selected_category, 'date_from' => $date_from, 'date_to' => $date_to]);
        }
    }

    public function createExpense()
    {
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.expenses.create', ['expense_categories' => $expense_categories]);
    }

    public function expenseCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Expense::create([
            'expense_category_id' => $data['expense_category_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message', 'You have successfully create a new expense.');
    }

    public function editExpense($id)
    {
        $expense_details = Expense::find($id);
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->get();
        return view('admin.expenses.edit', ['expense_categories' => $expense_categories, 'expense_details' => $expense_details]);
    }

    public function expenseUpdate(Request $request, $id)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        Expense::where('id', $id)->update([
            'expense_category_id' => $data['expense_category_id'],
            'date' => strtotime($data['date']),
            'amount' => $data['amount'],
            'school_id' => auth()->user()->school_id,
            'session_id' => $active_session,
        ]);

        return redirect()->back()->with('message', 'You have successfully update expense.');
    }

    public function expenseDelete($id)
    {
        $expense = Expense::find($id);
        $expense->delete();
        return redirect()->back()->with('message', 'You have successfully delete expense.');
    }


    /**
     * Show the expense category list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function expenseCategoryList()
    {
        $expense_categories = ExpenseCategory::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('admin.expense_category.expense_category_list', compact('expense_categories'));
    }

    public function createExpenseCategory()
    {
        return view('admin.expense_category.create');
    }

    public function expenseCategoryCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_category_check = ExpenseCategory::get()->where('name', $data['name']);

        if (count($duplicate_category_check) == 0) {

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            ExpenseCategory::create([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

            return redirect()->back()->with('message', 'You have successfully create a new expense category.');
        } else {
            return back()
                ->with('error', 'Sorry this expense category already exists');
        }
    }

    public function editExpenseCategory($id)
    {
        $expense_category = ExpenseCategory::find($id);
        return view('admin.expense_category.edit', ['expense_category' => $expense_category]);
    }

    public function expenseCategoryUpdate(Request $request, $id)
    {
        $data = $request->all();

        $duplicate_category_check = ExpenseCategory::get()->where('name', $data['name']);

        if (count($duplicate_category_check) == 0) {

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            ExpenseCategory::where('id', $id)->update([
                'name' => $data['name'],
                'school_id' => auth()->user()->school_id,
                'session_id' => $active_session,
            ]);

            return redirect()->back()->with('message', 'You have successfully update expense category.');
        } else {
            return back()
                ->with('error', 'Sorry this expense category already exists');
        }
    }

    public function expenseCategoryDelete($id)
    {
        $expense_category = ExpenseCategory::find($id);
        $expense_category->delete();
        return redirect()->back()->with('message', 'You have successfully delete expense category.');
    }

    /**
     * Show the book list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function bookList(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {

            $books = Book::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id);
            })->orWhere(function ($query) use ($search) {
                $query->where('author', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id);
            })->paginate(10);
        } else {
            $books = Book::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.book.list', compact('books', 'search'));
    }

    public function createBook()
    {
        return view('admin.book.create');
    }

    public function bookCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_book_check = Book::get()->where('name', $data['name']);

        if (count($duplicate_book_check) == 0) {

            $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

            $data['school_id'] = auth()->user()->school_id;
            $data['session_id'] = $active_session;
            $data['timestamp'] = strtotime(date('d-M-Y'));

            Book::create($data);

            return redirect()->back()->with('message', 'You have successfully create a book.');
        } else {
            return back()
                ->with('error', 'Sorry this book already exists');
        }
    }

    public function editBook($id = "")
    {
        $book_details = Book::find($id);
        return view('admin.book.edit', ['book_details' => $book_details]);
    }

    public function bookUpdate(Request $request, $id = '')
    {
        $data = $request->all();

        $duplicate_book_check = Book::get()->where('name', $data['name']);

        if (count($duplicate_book_check) == 0) {
            Book::where('id', $id)->update([
                'name' => $data['name'],
                'author' => $data['author'],
                'copies' => $data['copies'],
                'timestamp' => strtotime(date('d-M-Y')),
            ]);

            return redirect()->back()->with('message', 'You have successfully update book.');
        } else {
            return back()
                ->with('error', 'Sorry this book already exists');
        }
    }

    public function bookDelete($id)
    {
        $book = Book::find($id);
        $book->delete();
        return redirect()->back()->with('message', 'You have successfully delete book.');
    }


    /**
     * Show the book list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function bookIssueList(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        if (count($request->all()) > 0) {

            $data = $request->all();

            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to  = strtotime($date[1] . ' 23:59:59');
            $book_issues = BookIssue::where('issue_date', '>=', $date_from)
                ->where('issue_date', '<=', $date_to)
                ->where('school_id', auth()->user()->school_id)
                ->where('session_id', $active_session)
                ->get();

            return view('admin.book_issue.book_issue', ['book_issues' => $book_issues, 'date_from' => $date_from, 'date_to' => $date_to]);
        } else {
            $date_from = strtotime(date('d-m-Y', strtotime('first day of this month')) . ' 00:00:00');
            $date_to = strtotime(date('d-m-Y', strtotime('last day of this month')) . ' 23:59:59');
            $book_issues = BookIssue::where('issue_date', '>=', $date_from)
                ->where('issue_date', '<=', $date_to)
                ->where('school_id', auth()->user()->school_id)
                ->where('session_id', $active_session)
                ->get();

            return view('admin.book_issue.book_issue', ['book_issues' => $book_issues, 'date_from' => $date_from, 'date_to' => $date_to]);
        }
    }

    public function createBookIssue()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $books = Book::get()->where('school_id', auth()->user()->school_id);
        return view('admin.book_issue.create', ['classes' => $classes, 'books' => $books]);
    }

    public function bookIssueCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['status'] = 0;
        $data['issue_date'] = strtotime($data['issue_date']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['timestamp'] = strtotime(date('d-M-Y'));

        BookIssue::create($data);

        return redirect()->back()->with('message', 'You have successfully issued a book.');
    }

    public function editBookIssue($id = "")
    {
        $book_issue_details = BookIssue::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $books = Book::get()->where('school_id', auth()->user()->school_id);
        return view('admin.book_issue.edit', ['book_issue_details' => $book_issue_details, 'classes' => $classes, 'books' => $books]);
    }

    public function bookIssueUpdate(Request $request, $id = "")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['issue_date'] = strtotime($data['issue_date']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['timestamp'] = strtotime(date('d-M-Y'));

        unset($data['_token']);

        BookIssue::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'Updated successfully.');
    }

    public function bookIssueReturn($id)
    {
        BookIssue::where('id', $id)->update([
            'status' => 1,
            'timestamp' => strtotime(date('d-M-Y')),
        ]);

        return redirect()->back()->with('message', 'Return successfully.');
    }

    public function bookIssueDelete($id)
    {
        $book_issue = BookIssue::find($id);
        $book_issue->delete();
        return redirect()->back()->with('message', 'You have successfully delete a issued book.');
    }


    /**
     * Show the noticeboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function noticeboardList()
    {
        $notices = Noticeboard::get()->where('school_id', auth()->user()->school_id);

        $events = array();

        foreach ($notices as $notice) {
            if ($notice['end_date'] != "") {
                if ($notice['start_date'] != $notice['end_date']) {
                    $end_date = strtotime($notice['end_date']) + 24 * 60 * 60;
                    $end_date = date('Y-m-d', $end_date);
                } else {
                    $end_date = date('Y-m-d', strtotime($notice['end_date']));
                }
            }

            if ($notice['end_date'] == "" && $notice['start_time'] == "" && $notice['end_time'] == "") {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date']))
                );
            } else if ($notice['start_time'] != "" && ($notice['end_date'] == "" && $notice['end_time'] == "")) {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])) . 'T' . $notice['start_time']
                );
            } else if ($notice['end_date'] != "" && ($notice['start_time'] == "" && $notice['end_time'] == "")) {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])),
                    'end' => $end_date
                );
            } else if ($notice['end_date'] != "" && $notice['start_time'] != "" && $notice['end_time'] != "") {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date'])) . 'T' . $notice['start_time'],
                    'end' => date('Y-m-d', strtotime($notice['end_date'])) . 'T' . $notice['end_time']
                );
            } else {
                $info = array(
                    'id' => $notice['id'],
                    'title' => $notice['notice_title'],
                    'start' => date('Y-m-d', strtotime($notice['start_date']))
                );
            }
            array_push($events, $info);
        }

        $events = json_encode($events);

        return view('admin.noticeboard.noticeboard', ['events' => $events]);
    }

    public function createNoticeboard()
    {
        return view('admin.noticeboard.create');
    }

    public function noticeboardCreate(Request $request)
    {
        $request->validate([
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['status'] = 1;
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
            $imageName = time() . '.' . $data['image']->extension();
            $data['image']->storeAs('assets/uploads/noticeboard', $imageName, 'public');
            $data['image']  = $imageName;
        }

        Noticeboard::create($data);

        return redirect()->back()->with('message', 'You have successfully create a notice.');
    }

    public function editNoticeboard($id = "")
    {
        $notice = Noticeboard::find($id);
        return view('admin.noticeboard.edit', ['notice' => $notice]);
    }

    public function noticeboardUpdate(Request $request, $id = "")
    {
        $request->validate([
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['status'] = 1;
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
            $old_image = (string) Noticeboard::where('id', $id)->value('image');
            $imageName = time() . '.' . $data['image']->extension();

            delete_upload_file('assets/uploads/noticeboard', $old_image);
            $data['image']->storeAs('assets/uploads/noticeboard', $imageName, 'public');
            $data['image']  = $imageName;
        }

        unset($data['_token']);

        Noticeboard::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'Updated successfully.');
    }

    public function noticeboardDelete($id = '')
    {
        $notice = Noticeboard::find($id);
        if ($notice) {
            delete_upload_file('assets/uploads/noticeboard', $notice->image);
        }
        $notice->delete();
        return redirect()->back()->with('message', 'You have successfully delete a notice.');
    }


    /**
     * Show the subscription.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function subscription(Request $request)
    {

        $if_pending_payment = PaymentHistory::where('user_id', auth()->user()->id)->where('status', 'pending')->get()->count();

        if (count($request->all()) > 0) {
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to  = strtotime($date[1] . ' 23:59:59');
            $subscriptions = Subscription::where('school_id', auth()->user()->school_id)
                ->where('date_added', '>=', $date_from)
                ->where('date_added', '<=', $date_to)
                ->get();
        } else {
            $date_from = strtotime('first day of january this year');
            $date_to = strtotime('last day of december this year');
            $subscriptions = Subscription::where('school_id', auth()->user()->school_id)
                ->where('date_added', '>=', $date_from)
                ->where('date_added', '<=', $date_to)
                ->get();
        }

        $subscription_details = Subscription::where(['school_id' => auth()->user()->school_id, 'active' => '1']);
        if ($subscription_details->get()->count() > 0) {
            $package_details = Package::find($subscription_details->first()->package_id);
        } else {
            $subscription_details = Subscription::where(['school_id' => auth()->user()->school_id, 'status' => '0']);
            if ($subscription_details->get()->count() > 0) {
                $package_details = Package::find($subscription_details->first()->package_id);
            } else {
                $package_details = '';
            }
        }
        return view('admin.subscription.subscription', ['if_pending_payment' => $if_pending_payment, 'subscriptions' => $subscriptions, 'subscription_details' => $subscription_details, 'package_details' => $package_details, 'date_from' => $date_from, 'date_to' => $date_to]);
    }

    public function subscriptionPurchase()
    {
        $packages = Package::where('status', 1)->get();
        return view('admin.subscription.purchase', ['packages' => $packages]);
    }

    public function upgreadeSubscription()
    {
        $packages = Package::where('status', 1)->get();
        return view('admin.subscription.upgrade_subscription', ['packages' => $packages]);
    }

    /**
     * Show the event list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function eventList(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {

            $events = FrontendEvent::where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%");
            })->paginate(10);
        } else {
            $events = FrontendEvent::where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.events.events', compact('events', 'search'));
    }

    public function createEvent()
    {
        return view('admin.events.create_event');
    }

    public function eventCreate(Request $request)
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['timestamp'] = strtotime($data['timestamp']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['created_by'] = auth()->user()->id;

        FrontendEvent::create($data);

        return redirect()->back()->with('message', 'You have successfully create a event.');
    }

    public function editEvent($id = "")
    {
        $event = FrontendEvent::find($id);
        return view('admin.events.edit_event', ['event' => $event]);
    }

    public function eventUpdate(Request $request, $id = "")
    {
        $data = $request->all();

        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');

        $data['timestamp'] = strtotime($data['timestamp']);
        $data['school_id'] = auth()->user()->school_id;
        $data['session_id'] = $active_session;
        $data['created_by'] = auth()->user()->id;

        unset($data['_token']);

        FrontendEvent::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'Updated successfully.');
    }

    public function eventDelete($id)
    {
        $event = FrontendEvent::find($id);
        $event->delete();
        return redirect()->back()->with('message', 'You have successfully delete a event.');
    }

    // Complain List
    function complainList()
    {
        return view('admin.complain.complainList');
    }




    public function schoolSettings()
    {
        $school_details = School::find(auth()->user()->school_id);
        return view('admin.settings.school_settings', ['school_details' => $school_details]);
    }

    public function studentNumberPatterns()
    {
        $school_details = School::find(auth()->user()->school_id);

        $patternAdmission = $school_details ? (string) ($school_details->admission_number_pattern ?: '{YYYY}-{SEQ:5}') : '{YYYY}-{SEQ:5}';
        $patternEnrollment = $school_details ? (string) ($school_details->enrollment_number_pattern ?: '{YYYY}-{SEQ:5}') : '{YYYY}-{SEQ:5}';

        $year = date('Y');
        $yy = substr($year, -2);
        $mm = date('m');

        $schoolId = (int) auth()->user()->school_id;
        $yearInt = (int) $year;

        $lastAdmission = (int) (DB::table('student_number_sequences')
            ->where('school_id', $schoolId)
            ->where('type', 'admission')
            ->where('year', $yearInt)
            ->value('last_seq') ?? 0);

        $lastEnrollment = (int) (DB::table('student_number_sequences')
            ->where('school_id', $schoolId)
            ->where('type', 'enrollment')
            ->where('year', $yearInt)
            ->value('last_seq') ?? 0);

        $nextAdmissionSeq = $lastAdmission + 1;
        $nextEnrollmentSeq = $lastEnrollment + 1;

        $makePreview = function (string $pattern, int $seqValue) use ($year, $yy, $mm): string {
            $pad = 5;
            if (preg_match('/\{SEQ:(\d{1,2})\}/', $pattern, $m)) {
                $n = (int) $m[1];
                $pad = ($n >= 1 && $n <= 12) ? $n : 5;
            }
            $seq = str_pad((string) $seqValue, $pad, '0', STR_PAD_LEFT);
            $out = str_replace(['{YYYY}', '{YY}', '{MM}'], [$year, $yy, $mm], $pattern);
            return preg_replace('/\{SEQ:\d{1,2}\}/', $seq, $out, 1);
        };

        // Previews do not increment sequences
        $preview = [
            'admission' => $makePreview($patternAdmission, $nextAdmissionSeq),
            'enrollment' => $makePreview($patternEnrollment, $nextEnrollmentSeq),
            'admission_last' => $lastAdmission,
            'enrollment_last' => $lastEnrollment,
            'admission_next_seq' => $nextAdmissionSeq,
            'enrollment_next_seq' => $nextEnrollmentSeq,
        ];

        return view('admin.settings.student_number_patterns', compact('school_details', 'preview'));
    }

    public function studentNumberPatternsUpdate(Request $request)
    {
        $data = $request->all();

        $admission = (string) ($data['admission_number_pattern'] ?? '');
        $enrollment = (string) ($data['enrollment_number_pattern'] ?? '');

        foreach (['Admission' => $admission, 'Enrollment' => $enrollment] as $label => $pattern) {
            $pattern = trim($pattern);
            if ($pattern === '') {
                continue;
            }
            if (strlen($pattern) > 60) {
                return redirect()->back()->with('error', "{$label} pattern is too long.");
            }

            preg_match_all('/\{SEQ:(\d{1,2})\}/', $pattern, $seqMatches);
            $seqCount = isset($seqMatches[0]) ? count($seqMatches[0]) : 0;
            if ($seqCount !== 1) {
                return redirect()->back()->with('error', "{$label} pattern must include exactly one {SEQ:n}.");
            }
            $pad = (int) ($seqMatches[1][0] ?? 0);
            if ($pad < 1 || $pad > 12) {
                return redirect()->back()->with('error', "{$label} pattern {SEQ:n} padding must be between 1 and 12.");
            }

            // Allow only known tokens
            $stripped = preg_replace('/\{(YYYY|YY|MM)\}/', '', $pattern);
            $stripped = preg_replace('/\{SEQ:\d{1,2}\}/', '', $stripped);
            if (preg_match('/\{[A-Z0-9_:-]+\}/', $stripped)) {
                return redirect()->back()->with('error', "{$label} pattern contains unsupported token(s). Allowed: {YYYY}, {YY}, {MM}, {SEQ:n}.");
            }
        }

        School::where('id', auth()->user()->school_id)->update([
            'admission_number_pattern' => $admission !== '' ? $admission : null,
            'enrollment_number_pattern' => $enrollment !== '' ? $enrollment : null,
        ]);

        return redirect()->back()->with('message', 'Student number patterns updated successfully.');
    }

    public function schoolUpdate(Request $request)
    {

        $data = $request->all();

        unset($data['_token']);

        $school_data = School::where('id', auth()->user()->school_id)->first();
        if ($request->school_logoo) {

            $old_image = $school_data->school_logo;

            $ext = $request->school_logoo->getClientOriginalExtension();
            $newFileName = random(8) . '.' . $ext;
            delete_upload_file('assets/uploads/school_logo', $old_image);
            $request->school_logoo->storeAs('assets/uploads/school_logo', $newFileName, 'public');
            $school_data->school_logo = $newFileName;
            $school_data->save();
        }

        if ($request->email_logo) {

            $old_image = $school_data->email_logo;

            $ext = $request->email_logo->getClientOriginalExtension();
            $newFileName = random(8) . '.' . $ext;
            delete_upload_file('assets/uploads/school_logo', $old_image);
            $request->email_logo->storeAs('assets/uploads/school_logo', $newFileName, 'public');
            $school_data->email_logo = $newFileName;
            $school_data->save();
        }
        if ($request->socialLogo1) {

            $old_image = $school_data->socialLogo1;

            $ext = $request->socialLogo1->getClientOriginalExtension();
            $newFileName = random(8) . '.' . $ext;
            delete_upload_file('assets/uploads/school_logo', $old_image);
            $request->socialLogo1->storeAs('assets/uploads/school_logo', $newFileName, 'public');
            $school_data->socialLogo1 = $newFileName;
            $school_data->save();
        }
        if ($request->socialLogo2) {

            $old_image = $school_data->socialLogo2;

            $ext = $request->socialLogo2->getClientOriginalExtension();
            $newFileName = random(8) . '.' . $ext;
            delete_upload_file('assets/uploads/school_logo', $old_image);
            $request->socialLogo2->storeAs('assets/uploads/school_logo', $newFileName, 'public');
            $school_data->socialLogo2 = $newFileName;
            $school_data->save();
        }
        if ($request->socialLogo3) {

            $old_image = $school_data->socialLogo3;

            $ext = $request->socialLogo3->getClientOriginalExtension();
            $newFileName = random(8) . '.' . $ext;
            delete_upload_file('assets/uploads/school_logo', $old_image);
            $request->socialLogo3->storeAs('assets/uploads/school_logo', $newFileName, 'public');
            $school_data->socialLogo3 = $newFileName;
            $school_data->save();
        }

        School::where('id', auth()->user()->school_id)->update([
            'title' => $data['school_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'email_title' => $data['email_title'],
            'email_details' => $data['email_details'],
            'warning_text' => $data['warning_text'],
            'socialLink1' => $data['socialLink1'],
            'socialLink2' => $data['socialLink2'],
            'socialLink3' => $data['socialLink3'],

        ]);


        return redirect()->back()->with('message', 'School details updated successfully.');
    }







    public function studentFeeinvoice($id)
    {
        $invoice_details = StudentFeeManager::find($id)->toArray();
        $student_details = (new CommonController)->get_student_details_by_id($invoice_details['student_id'])->toArray();


        return view('admin.student_fee_manager.invoice', ['invoice_details' => $invoice_details, 'student_details' => $student_details]);
    }

    public function offline_payment_pending(Request $request)
    {
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        if (count($request->all()) > 0) {
            $data = $request->all();
            $date = explode('-', $data['eDateRange']);
            $date_from = strtotime($date[0] . ' 00:00:00');
            $date_to  = strtotime($date[1] . ' 23:59:59');
            $selected_class = $data['class'];
            $selected_status = 'pending';



            if ($selected_class != "all" && $selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_class != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('class_id', $selected_class)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else if ($selected_status != "all") {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', $selected_status)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            } else {
                $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            }


            $classes = Classes::where('school_id', auth()->user()->school_id)->get();

            return view('admin.student_fee_manager.student_fee_manager_pending', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
        } else {
            $classes = Classes::where('school_id', auth()->user()->school_id)->get();
            $date_from = strtotime(date('d-m-Y', strtotime('first day of this month')) . ' 00:00:00');
            $date_to = strtotime(date('d-m-Y', strtotime('last day of this month')) . ' 23:59:59');
            $selected_class = "";
            $selected_status = "";
            $invoices = StudentFeeManager::where('timestamp', '>=', $date_from)->where('timestamp', '<=', $date_to)->where('status', 'pending')->where('school_id', auth()->user()->school_id)->where('session_id', $active_session)->get();
            return view('admin.student_fee_manager.student_fee_manager_pending', ['classes' => $classes, 'invoices' => $invoices, 'date_from' => $date_from, 'date_to' => $date_to, 'selected_class' => $selected_class, 'selected_status' => $selected_status]);
        }
    }

    public function update_offline_payment($id, $status)
    {

        $amount = StudentFeeManager::find($id)->toArray();
        $amount = $amount['total_amount'];

        $studentFeeManager = StudentFeeManager::find($id);
        $students_id = User::find($studentFeeManager->student_id);
        $student_email = $students_id->email;
        $parents_id = User::find($studentFeeManager->parent_id);

        if (!empty($parents_id)) {
            $parents_email = $parents_id->email;
        }

        if ($status == 'approve') {
            $studentsemail = StudentFeeManager::where('id', $id)->update([
                'status' => 'paid',
                'updated_at' => date("Y-m-d H:i:s"),
                'paid_amount' => $amount,
                'payment_method' => 'offline'
            ]);

            if (!empty(get_settings('smtp_user')) && (get_settings('smtp_pass')) && (get_settings('smtp_host')) && (get_settings('smtp_port'))) {
                if (!empty($parents_id)) {
                    Mail::to($student_email)->send(new StudentsEmail($studentFeeManager));
                    Mail::to($parents_email)->send(new StudentsEmail($studentFeeManager));
                } else {
                    Mail::to($student_email)->send(new StudentsEmail($studentFeeManager));
                }
            }

            return redirect()->back()->with('message', 'Payment Approved');
        } elseif ($status == 'decline') {
            StudentFeeManager::where('id', $id)->update([
                'status' => 'unpaid',
                'updated_at' => date("Y-m-d H:i:s"),
                'paid_amount' => $amount,
                'payment_method' => 'offline'
            ]);

            return redirect()->back()->with('message', 'Payment Decline');
        }
    }

    public function paymentSettings()
    {

        $payment_gateways = PaymentMethods::where('school_id', auth()->user()->school_id)->get();

        $school_currency = School::where('id', auth()->user()->school_id)->first()->toArray();
        $currencies = Currency::all()->toArray();
        $paypal = "";
        $paypal_keys = "";
        $stripe = "";
        $stripe_keys = "";
        $razorpay = "";
        $razorpay_keys = "";
        $paytm = "";
        $paytm_keys = "";
        $flutterwave = "";
        $flutterwave_keys = "";
        $paystack = "";
        $paystack_keys = "";

        foreach ($payment_gateways as  $single_gateway) {

            if ($single_gateway->name == "paypal") {

                $paypal = $single_gateway->toArray();
                $paypal_keys = json_decode($paypal['payment_keys']);
            } elseif ($single_gateway->name == "stripe") {
                $stripe = $single_gateway->toArray();
                $stripe_keys = json_decode($stripe['payment_keys']);
            } elseif ($single_gateway->name == "razorpay") {
                $razorpay = $single_gateway->toArray();
                $razorpay_keys = json_decode($razorpay['payment_keys']);
            } elseif ($single_gateway->name == "paytm") {
                $paytm = $single_gateway->toArray();
                $paytm_keys = json_decode($paytm['payment_keys']);
            } elseif ($single_gateway->name == "flutterwave") {
                $flutterwave = $single_gateway->toArray();
                $flutterwave_keys = json_decode($flutterwave['payment_keys']);
            } elseif ($single_gateway->name == "paystack") {
                $paystack = $single_gateway->toArray();
                $paystack_keys = json_decode($paystack['payment_keys']);
            }
        }

        return view('admin.payment_settings.key_settings', ['paytm' => $paytm, 'paytm_keys' => $paytm_keys, 'razorpay' => $razorpay, 'razorpay_keys' => $razorpay_keys, 'stripe' => $stripe, 'stripe_keys' => $stripe_keys, 'paypal' => $paypal, 'paypal_keys' => $paypal_keys, 'flutterwave' => $flutterwave, 'flutterwave_keys' => $flutterwave_keys, 'paystack' => $paystack, 'paystack_keys' => $paystack_keys, 'school_currency' => $school_currency, 'currencies' => $currencies]);
    }

    public function install_paystack()
    {
        $keys = array();
        $paystack = new PaymentMethods;
        $paystack['name'] = "paystack";
        $paystack['image'] = "paystack.png";
        $paystack['status'] = 1;
        $paystack['mode'] = "test";
        $keys['test_key'] = "pk_test_xxxxxxxxxxxxx";
        $keys['test_secret_key'] = "sk_test_xxxxxxxxxxxxxxx";
        $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
        $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
        $paystack['payment_keys'] = json_encode($keys);
        $paystack['school_id'] = auth()->user()->school_id;
        $paystack->save();
    }


    public function paymentSettings_post(Request $request)
    {
        $data = $request->all();

        unset($data['_token']);

        $school_data = School::where('id', auth()->user()->school_id)->first();

        if ($request->off_pay_ins_file || $request->off_pay_ins_text) {

            if ($request->off_pay_ins_file) {

                $old_image = $school_data->off_pay_ins_file;

                $ext = $request->off_pay_ins_file->getClientOriginalExtension();
                $newFileName = random(8) . '.' . $ext;
                delete_upload_file('assets/uploads/offline_payment', $old_image);
                $request->off_pay_ins_file->storeAs('assets/uploads/offline_payment', $newFileName, 'public');
                $school_data->off_pay_ins_file = $newFileName;
                $school_data->save();
            }

            School::where('id', auth()->user()->school_id)->update([
                'off_pay_ins_text' => $data['off_pay_ins_text'],

            ]);

            return redirect()->back()->with('message', 'Offline payment instruction update.');
        }
        $method = $data['method'];
        $update_id = $data['update_id'];


        if ($method == 'currency') {
            $Currency = School::find($update_id);
            $Currency['school_currency'] = $data['school_currency'];
            $Currency['currency_position'] = $data['currency_position'];
            $Currency->save();
        } elseif ($method == 'paypal') {

            $keys = array();
            $paypal = PaymentMethods::find($update_id);
            $paypal['status'] = $data['status'];
            $paypal['mode'] = $data['mode'];
            $keys['test_client_id'] = $data['test_client_id'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['live_client_id'] = $data['live_client_id'];
            $keys['live_secret_key'] = $data['live_secret_key'];
            $paypal['payment_keys'] = json_encode($keys);
            $paypal['school_id'] = auth()->user()->school_id;
            $paypal->save();
        } elseif ($method == 'stripe') {
            $keys = array();
            $stripe = PaymentMethods::find($update_id);
            $stripe['status'] = $data['status'];
            $stripe['mode'] = $data['mode'];
            $keys['test_key'] = $data['test_key'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['public_live_key'] = $data['public_live_key'];
            $keys['secret_live_key'] = $data['secret_live_key'];
            $stripe['payment_keys'] = json_encode($keys);
            $stripe['school_id'] = auth()->user()->school_id;
            $stripe->save();
        } elseif ($method == 'razorpay') {
            $keys = array();
            $razorpay = PaymentMethods::find($update_id);
            $razorpay['status'] = $data['status'];
            $razorpay['mode'] = $data['mode'];
            $keys['test_key'] = $data['test_key'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['live_key'] = $data['live_key'];
            $keys['live_secret_key'] = $data['live_secret_key'];
            $keys['theme_color'] = $data['theme_color'];
            $razorpay['payment_keys'] = json_encode($keys);
            $razorpay['school_id'] = auth()->user()->school_id;
            $razorpay->save();
        } elseif ($method == 'paytm') {
            $keys = array();
            $paytm = PaymentMethods::find($update_id);
            $paytm['status'] = $data['status'];
            $paytm['mode'] = $data['mode'];
            $keys['test_merchant_id'] = $data['test_merchant_id'];
            $keys['test_merchant_key'] = $data['test_merchant_key'];
            $keys['live_merchant_id'] = $data['live_merchant_id'];
            $keys['live_merchant_key'] = $data['live_merchant_key'];
            $keys['environment'] = $data['environment'];
            $keys['merchant_website'] = $data['merchant_website'];
            $keys['channel'] = $data['channel'];
            $keys['industry_type'] = $data['industry_type'];
            $paytm['payment_keys'] = json_encode($keys);
            $paytm['school_id'] = auth()->user()->school_id;
            $paytm->save();
        } elseif ($method == 'flutterwave') {
            $keys = array();
            $flutterwave = PaymentMethods::find($update_id);
            $flutterwave['status'] = $data['status'];
            $flutterwave['mode'] = $data['mode'];
            $keys['test_key'] = $data['test_key'];
            $keys['test_secret_key'] = $data['test_secret_key'];
            $keys['test_encryption_key'] = $data['test_encryption_key'];
            $keys['public_live_key'] = $data['public_live_key'];
            $keys['secret_live_key'] = $data['secret_live_key'];
            $keys['encryption_live_key'] = $data['encryption_live_key'];
            $flutterwave['payment_keys'] = json_encode($keys);
            $flutterwave['school_id'] = auth()->user()->school_id;
            $flutterwave->save();
        } elseif ($method == 'paystack') {
            $keys = array();
            $paystack = new PaymentMethods;
            $paystack['name'] = "paystack";
            $paystack['image'] = "paystack.png";
            $paystack['status'] = 1;
            $paystack['mode'] = "test";
            $keys['test_key'] = "pk_test_xxxxxxxxxxx";
            $keys['test_secret_key'] = "sk_test_xxxxxxxxxxx";
            $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
            $paystack['payment_keys'] = json_encode($keys);
            $paystack['school_id'] = auth()->user()->school_id;
            $paystack->save();
        }

        return redirect()->route('admin.settings.payment')->with('message', 'key has been updated');
    }

    public function insert_gateways()
    {
        $paypal = PaymentMethods::where(array('name' => 'paypal', 'school_id' => auth()->user()->school_id))->first();

        if (empty($paypal)) {
            $keys = array();
            $paypal = new PaymentMethods;
            $paypal['name'] = "paypal";
            $paypal['image'] = "paypal.png";
            $paypal['status'] = 1;
            $paypal['mode'] = "test";
            $keys['test_client_id'] = "snd_cl_id_xxxxxxxxxxxxx";
            $keys['test_secret_key'] = "snd_cl_sid_xxxxxxxxxxxx";
            $keys['live_client_id'] = "lv_cl_id_xxxxxxxxxxxxxxx";
            $keys['live_secret_key'] = "lv_cl_sid_xxxxxxxxxxxxxx";
            $paypal['payment_keys'] = json_encode($keys);
            $paypal['school_id'] = auth()->user()->school_id;
            $paypal->save();
        }

        $stripe = PaymentMethods::where(array('name' => 'stripe', 'school_id' => auth()->user()->school_id))->first();

        if (empty($stripe)) {
            $keys = array();
            $stripe = new PaymentMethods;
            $stripe['name'] = "stripe";
            $stripe['image'] = "stripe.png";
            $stripe['status'] = 1;
            $stripe['mode'] = "test";
            $keys['test_key'] = "pk_test_xxxxxxxxxxxxx";
            $keys['test_secret_key'] = "sk_test_xxxxxxxxxxxxxx";
            $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
            $stripe['payment_keys'] = json_encode($keys);
            $stripe['school_id'] = auth()->user()->school_id;
            $stripe->save();
        }


        $razorpay = PaymentMethods::where(array('name' => 'razorpay', 'school_id' => auth()->user()->school_id))->first();

        if ((empty($razorpay))) {
            $keys = array();
            $razorpay = new PaymentMethods;
            $razorpay['name'] = "razorpay";
            $razorpay['image'] = "razorpay.png";
            $razorpay['status'] = 1;
            $razorpay['mode'] = "test";
            $keys['test_key'] = "rzp_test_xxxxxxxxxxxxx";
            $keys['test_secret_key'] = "rzs_test_xxxxxxxxxxxxx";
            $keys['live_key'] = "rzp_live_xxxxxxxxxxxxx";
            $keys['live_secret_key'] = "rzs_live_xxxxxxxxxxxxx";
            $keys['theme_color'] = "#c7a600";
            $razorpay['payment_keys'] = json_encode($keys);
            $razorpay['school_id'] = auth()->user()->school_id;
            $razorpay->save();
        }

        $paytm = PaymentMethods::where(array('name' => 'paytm', 'school_id' => auth()->user()->school_id))->first();

        if (empty($paytm)) {
            $keys = array();
            $paytm = new PaymentMethods;
            $paytm['name'] = "paytm";
            $paytm['image'] = "paytm.png";
            $paytm['status'] = 1;
            $paytm['mode'] = "test";
            $keys['test_merchant_id'] = "tm_id_xxxxxxxxxxxx";
            $keys['test_merchant_key'] = "tm_key_xxxxxxxxxx";
            $keys['live_merchant_id'] = "lv_mid_xxxxxxxxxxx";
            $keys['live_merchant_key'] = "lv_key_xxxxxxxxxxx";
            $keys['environment'] = "provide-a-environment";
            $keys['merchant_website'] = "merchant-website";
            $keys['channel'] = "provide-channel-type";
            $keys['industry_type'] = "provide-industry-type";
            $paytm['payment_keys'] = json_encode($keys);
            $paytm['school_id'] = auth()->user()->school_id;
            $paytm->save();
        }

        $flutterwave = PaymentMethods::where(array('name' => 'flutterwave', 'school_id' => auth()->user()->school_id))->first();

        if (empty($flutterwave)) {
            $keys = array();
            $flutterwave = new PaymentMethods;
            $flutterwave['name'] = "flutterwave";
            $flutterwave['image'] = "flutterwave.png";
            $flutterwave['status'] = 1;
            $flutterwave['mode'] = "test";
            $keys['test_key'] = "flwp_test_xxxxxxxxxxxxx";
            $keys['test_secret_key'] = "flws_test_xxxxxxxxxxxxx";
            $keys['test_encryption_key'] = "flwe_test_xxxxxxxxxxxxx";
            $keys['public_live_key'] = "flwp_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key'] = "flws_live_xxxxxxxxxxxxxx";
            $keys['encryption_live_key'] = "flwe_live_xxxxxxxxxxxxxx";
            $flutterwave['payment_keys'] = json_encode($keys);
            $flutterwave['school_id'] = auth()->user()->school_id;
            $flutterwave->save();
        }

        $paystack = PaymentMethods::where(array('name' => 'paystack', 'school_id' => auth()->user()->school_id))->first();

        if (empty($paystack)) {
            $keys = array();
            $paystack = new PaymentMethods;
            $paystack['name'] = "paystack";
            $paystack['image'] = "paystack.png";
            $paystack['status'] = 1;
            $paystack['mode'] = "test";
            $keys['test_key'] = "pk_test_xxxxxxxxxx";
            $keys['test_secret_key'] = "sk_test_xxxxxxxxxxxxxx";
            $keys['public_live_key'] = "pk_live_xxxxxxxxxxxxxx";
            $keys['secret_live_key'] = "sk_live_xxxxxxxxxxxxxx";
            $paystack['payment_keys'] = json_encode($keys);
            $paystack['school_id'] = auth()->user()->school_id;
            $paystack->save();
        }
    }

    public function subscriptionPayment($package_id)
    {

        $selected_package = Package::find($package_id)->toArray();
        $user_info = User::where('id', auth()->user()->id)->first()->toArray();

        if ($selected_package['price'] == 0) {
            $check_duplication = Subscription::where('package_id', $selected_package['id'])->where('school_id', auth()->user()->school_id)->get()->count();
            if ($check_duplication == 0) {
                return redirect()->route('admin_free_subcription', ['user_id' => auth()->user()->id, 'package_id' => $selected_package['id']]);
            } else {
                return redirect()->back()->with('error', 'you can not subscribe the free trail twice');
            }
        }

        return view('admin.subscription.payment_gateway', ['selected_package' => $selected_package, 'user_info' => $user_info]);
    }

    public function admin_free_subcription(Request $request)
    {
        $data = $request->all();

        $selected_package = Package::find($data['package_id'])->toArray();
        $user_info = User::where('id', $data['user_id'])->first()->toArray();
        $school_email = School::where('id', auth()->user()->school_id)->value('email');


        $data['document_file'] = "sample-payment.pdf";

        $transaction_keys = json_encode($data);
        if ($selected_package['package_type'] == 'life_time') {
            $status = Subscription::create([
                'package_id' => $selected_package['id'],
                'school_id' => auth()->user()->school_id,
                'paid_amount' => $selected_package['price'],
                'payment_method' => 'free',
                'transaction_keys' => $transaction_keys,
                'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                'expire_date' => 'life_time',
                'studentLimit' => $selected_package['studentLimit'],
                'status' => '1',
                'active' => '1',
            ]);
        } else {
            $status = Subscription::create([
                'package_id' => $selected_package['id'],
                'school_id' => auth()->user()->school_id,
                'paid_amount' => $selected_package['price'],
                'payment_method' => 'free',
                'transaction_keys' => $transaction_keys,
                'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                'expire_date' => strtotime('+' . $selected_package['days'] . ' days', strtotime(date("Y-m-d H:i:s"))),
                'studentLimit' => $selected_package['studentLimit'],
                'status' => '1',
                'active' => '1',
            ]);
        }


        Mail::to($school_email)->send(new FreeEmail($status));


        return redirect()->route('admin.subscription')->with('message', 'Free Subscription Completed Successfully');
    }




    public function admin_subscription_offline_payment(Request $request, $id = "")
    {
        $data = $request->all();

        if ($data['amount'] > 0) {

            $file = $data['document_image'];

            if ($file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file


                $file->storeAs('assets/uploads/offline_payment', $filename, 'public');
                $data['document_image'] = $filename;
            } else {
                $data['document_image'] = '';
            }

            $pending_payment = new PaymentHistory;

            $pending_payment['payment_type'] = 'subscription';
            $pending_payment['user_id'] = auth()->user()->id;
            $pending_payment['package_id'] = $id;
            $pending_payment['amount'] = $data['amount'];
            $pending_payment['school_id'] = auth()->user()->school_id;
            $pending_payment['transaction_keys'] = '[]';
            $pending_payment['document_image'] = $data['document_image'];
            $pending_payment['paid_by'] = 'offline';
            $pending_payment['status'] = 'pending';
            $pending_payment['timestamp'] = strtotime(date("Y-m-d H:i:s"));

            $pending_payment->save();


            return redirect()->route('admin.subscription')->with('message', 'offline payment requested successfully');
        } else {
            return redirect()->route('admin.subscription')->with('message', 'offline payment requested fail');
        }
    }

    public function offlinePayment(Request $request, $id = "")
    {
        $data = $request->all();

        if ($data['amount'] > 0) :

            $file = $data['document_image'];

            if ($file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file


                $file->storeAs('assets/uploads/offline_payment', $filename, 'public');
                $data['document_image'] = $filename;
            } else {
                $data['document_image'] = '';
            }


            PaymentHistory::create([
                'payment_type' => 'subscription',
                'user_id' => auth()->user()->id,
                'amount' => $data['amount'],
                'school_id' => $id,
                'transaction_keys' => json_encode(array()),
                'document_image' => $data['document_image'],
                'paid_by' => 'offline',
                'status' => 'pending',
                'timestamp' => strtotime(date('Y-m-d')),
            ]);

            return redirect('admin/subscription')->with('message', 'Your document will be reviewd.');

        else :
            return redirect('admin/subscription')->with('warning', 'Session timed out. Please try again');
        endif;
    }


    function profile()
    {
        return view('admin.profile.view');
    }

    function profile_update(Request $request)
    {
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['designation'] = $request->designation;

        $user_info['birthday'] = strtotime($request->eDefaultDateRange);
        $user_info['gender'] = $request->gender;
        $user_info['phone'] = $request->phone;
        $user_info['address'] = $request->address;


        if (empty($request->photo)) {
            $user_info['photo'] = $request->old_photo;
        } else {
            delete_upload_file('assets/uploads/user-images', $request->old_photo);

            $file_name = random(10) . '.png';
            $user_info['photo'] = $file_name;

            $request->photo->storeAs('assets/uploads/user-images', $file_name, 'public');
        }

        $data['user_information'] = json_encode($user_info);

        User::where('id', auth()->user()->id)->update($data);

        return redirect(route('admin.profile'))->with('message', get_phrase('Profile info updated successfully'));
    }

    function user_language(Request $request)
    {
        $data['language'] = $request->language;
        User::where('id', auth()->user()->id)->update($data);

        return redirect()->back()->with('message', 'You have successfully transleted language.');
    }

    function password($action_type = null, Request $request)
    {



        if ($action_type == 'update') {



            if ($request->new_password != $request->confirm_password) {
                return back()->with("error", "Confirm Password Doesn't match!");
            }


            if (!Hash::check($request->old_password, auth()->user()->password)) {
                return back()->with("error", "Current Password Doesn't match!");
            }

            $data['password'] = Hash::make($request->new_password);
            User::where('id', auth()->user()->id)->update($data);

            return redirect(route('admin.password', 'edit'))->with('message', get_phrase('Password changed successfully'));
        }

        return view('admin.profile.password');
    }



    /**
     * Show the session manager.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function sessionManager()
    {
        $sessions = Session::where('school_id', auth()->user()->school_id)->get();
        return view('admin.session.session_manager', ['sessions' => $sessions]);
    }

    public function activeSession($id)
    {
        $previous_session_id = get_school_settings(auth()->user()->school_id)->value('running_session');

        Session::where('id', $previous_session_id)->update([
            'status' => '0',
        ]);

        $session = Session::where('id', $id)->update([
            'status' => '1',
        ]);

        School::where('id', auth()->user()->school_id)->update([
            'running_session' => $id,
        ]);

        $response = array(
            'status' => true,
            'notification' => get_phrase('Session has been activated')
        );
        $response = json_encode($response);

        echo $response;
    }

    public function createSession()
    {
        return view('admin.session.create');
    }

    public function sessionCreate(Request $request)
    {
        $data = $request->all();

        $duplicate_session_check = Session::get()->where('session_title', $data['session_title'])->where('school_id', auth()->user()->school_id);

        if (count($duplicate_session_check) == 0) {

            $data['status'] = '0';
            $data['school_id'] = auth()->user()->school_id;

            Session::create($data);

            return redirect()->back()->with('message', 'You have successfully create a session.');
        } else {
            return redirect()->back()->with('error', 'Sorry this session already exists');
        }
    }

    public function editSession($id = '')
    {
        $session = Session::find($id);
        return view('admin.session.edit', ['session' => $session]);
    }

    public function sessionUpdate(Request $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        Session::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully update session.');
    }

    public function sessionDelete($id = '')
    {
        $previous_session_id = get_school_settings(auth()->user()->school_id)->value('running_session');

        if ($previous_session_id != $id) {
            $session = Session::find($id);
            $session->delete();
            return redirect()->back()->with('message', 'You have successfully delete a session.');
        } else {
            return redirect()->back()->with('error', 'Can not delete active session.');
        }
    }

    // Account Disable
    public function account_disable($id)
    {
        User::where('id', $id)->update([
            'account_status' => 'disable',
        ]);
        return redirect()->back()->with('message', 'Account Disable Successfully');
    }

    // Account Enable
    public function account_enable($id)
    {
        User::where('id', $id)->update([
            'account_status' => 'enable',
        ]);
        return redirect()->back()->with('message', 'Account Enable Successfully');
    }

    public function feedback_list()
    {
        $feedbacks = Feedback::where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->paginate(20);
        return view('admin.feedback.feedback_list', ['feedbacks' => $feedbacks]);
    }

    public function create_feedback()
    {
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.feedback.create_feedback', ['classes' => $classes]);
    }

    public function upload_feedback(Request $request)
    {
        $data = $request->all();
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        //$admin_id = auth()->user()->id;

        $feedbackData = [
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'student_id' => isset($data['student_id'][0]) ? $data['student_id'][0] : null, // Assuming single student for feedback
            'parent_id' => isset($data['parent_id'][0]) ? $data['parent_id'][0] : null, // Assuming single parent for feedback
            'feedback_text' => $data['feedback_text'],
            'school_id' => auth()->user()->school_id,
            'admin_id' => auth()->user()->id,
            'session_id' => $active_session,
            'title' => $data['title']

        ];

        // Create feedback entry
        Feedback::create($feedbackData);

        return redirect()->back()->with('message', 'Feedback Sent Successfully');
    }

    public function edit_feedback($id)
    {

        $feedback = Feedback::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.feedback.edit_feedback', ['classes' => $classes],  ['feedback' => $feedback]);
    }

    public function update_feedback(Request $request, $id)
    {
        $data = $request->all();

        unset($data['_token']);

        Feedback::where('id', $id)->update($data);

        return redirect()->back()->with('message', 'You have successfully update feedback.');
    }

    public function delete_feedback($id)
    {
        Feedback::where('id', $id)->delete();
        return redirect()->back()->with('message', 'Delete successfully.');
    }

    //  Message

    public function allMessage(Request $request, $id)
    {

        $msg_user_details = DB::table('users')
            ->join('message_thrades', function ($join) {
                // Join where the user is the sender
                $join->on('users.id', '=', 'message_thrades.sender_id')
                    ->orWhere(function ($query) {
                        // Join where the user is the receiver
                        $query->on('users.id', '=', 'message_thrades.reciver_id');
                    });
            })
            ->select('users.id as user_id', 'message_thrades.id as thread_id', 'users.*', 'message_thrades.*')
            ->where('message_thrades.id', $id)
            ->where('message_thrades.school_id', auth()->user()->school_id)
            ->where('users.id', '<>', auth()->user()->id) // Exclude the authenticated user
            ->first();



        if ($request->ajax()) {
            $query = $request->input('query');

            // Search users by name or any other criteria
            $users = User::where('name', 'LIKE', "%{$query}%")
                ->where('school_id', auth()->user()->school_id)
                ->get();

            // Prepare HTML response
            $html = '';

            // Check if any users were found
            if ($users->isEmpty()) {
                return response()->json('No User found');
            }

            foreach ($users as $user) {

                if (!empty($user)) {
                    $userInfo = json_decode($user->user_information);

                    $user_image = !empty($userInfo->photo)
                        ? asset('assets/uploads/user-images/' . $userInfo->photo)
                        : asset('assets/uploads/user-images/thumbnail.png');

                    $html .= '
                        <div class="user-item d-flex align-items-center msg_us_src_list">
                            <a href="' . route('admin.message.messagethrades', ['id' => $user->id]) . '">
                                <img src="' . $user_image . '" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                                <span class="ms-3">' . $user->name . '</span>
                            </a>
                        </div>
                    ';
                }
            }

            return response()->json($html);
        }


        $chat_datas = Chat::where('school_id', auth()->user()->school_id)->get();

        $counter_condition = Chat::where('message_thrade', $id)->orderBy('id', 'desc')->first();


        if (!empty($counter_condition->sender_id)) {
            if ($counter_condition->sender_id != auth()->user()->id) {
                Chat::where('message_thrade', $id)->update(['read_status' => 1]);
            }
        }


        return view('admin.message.all_message', ['msg_user_details' => $msg_user_details], ['chat_datas' => $chat_datas]);
    }

    public function messagethrades($id)
    {

        $exists = MessageThrade::where('reciver_id', $id)
            ->where('sender_id', auth()->user()->id)
            ->exists();
        if ($id != auth()->user()->id) {
            if (!$exists) {
                $message_thrades_data = [
                    'reciver_id' => $id,
                    'sender_id' => auth()->user()->id,
                    'school_id' => auth()->user()->school_id,
                ];

                MessageThrade::create($message_thrades_data);

                //return redirect()->back()->with('message', 'User added successfully');
            }


            $message_thrades = MessageThrade::where('reciver_id', $id)
                ->where('sender_id', auth()->user()->id)
                ->first();
            $msg_trd_id = $message_thrades->id;

            $msg_user_details = DB::table('users')
                ->join('message_thrades', 'users.id', '=', 'message_thrades.reciver_id')
                ->select('users.id as user_id', 'message_thrades.id as thread_id', 'users.*', 'message_thrades.*')
                ->where('message_thrades.id', $msg_trd_id)
                ->first();

            $chat_datas = Chat::where('school_id', auth()->user()->school_id)->get();

            // Combine all data into a single array
            return view('admin.message.all_message', ['id' => $msg_trd_id, 'msg_user_details' => $msg_user_details, 'chat_datas' => $chat_datas,]);
        }
        return redirect()->back()->with('error', 'You can not add you');
    }


    public function chat_save(Request $request)
    {
        $data = $request->all();
        $chat_data = [
            'message_thrade' => $data['message_thrade'],
            'reciver_id' => $data['reciver_id'],
            'message' => $data['message'],
            'school_id' => auth()->user()->school_id,
            'sender_id' => auth()->user()->id,
            'read_status' => 0,

        ];

        // Create chat entry
        $chat = Chat::create($chat_data);
        
        // Load relationships
        $chat->load('sender', 'receiver');
        $sender = User::find(auth()->user()->id);
        $receiver = User::find($data['reciver_id']);
        
        // Broadcast the message event
        broadcast(new MessageSent($chat, $sender, $receiver))->toOthers();

        return redirect()->back();
    }

    public function chat_empty(Request $request)
    {

        if ($request->ajax()) {
            $query = $request->input('query');

            $users = User::where('name', 'LIKE', "%{$query}%")
                ->where('school_id', auth()->user()->school_id)
                ->get();

            $html = '';

            if ($users->isEmpty()) {
                return response()->json('No User found');
            }

            foreach ($users as $user) {
                $userInfo = json_decode($user->user_information);
                $user_image = !empty($userInfo->photo)
                    ? asset('assets/uploads/user-images/' . $userInfo->photo)
                    : asset('assets/uploads/user-images/thumbnail.png');

                $html .= '
                    <div class="user-item d-flex align-items-center msg_us_src_list">
                        <a href="' . route('admin.message.messagethrades', ['id' => $user->id]) . '">
                            <img src="' . $user_image . '" alt="User Image" style="width: 50px; height: 50px; border-radius: 50%;">
                            <span class="ms-3">' . $user->name . '</span>
                        </a>
                    </div>
                ';
            }

            return response()->json($html);
        }

        // Pass the data to the view only if msg_user_details is not null
        return view('admin.message.chat_empty');
    }


    // Appraisal

    public function appraisalQustions()
    {
        $appraisals = Appraisal::where('school_id', auth()->user()->school_id)->paginate(10);
        return view('admin.appraisal.appraisalQustions', ['appraisals' => $appraisals]);
    }

    public function createQustion()
    {
        $teachers = User::get()->where('role_id', 3)->where('school_id', auth()->user()->school_id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        return view('admin.appraisal.createQustion', ['classes' => $classes], ['teachers' => $teachers]);
    }

    public function storeQustion(Request $request)
    {

        $request->validate([
            'class_id'    => 'required|integer',
            'teacher_id'  => 'required|array',
            'teacher_id.*' => 'integer',
            'ans_type'    => 'required|string|in:mcq,rating,binary,text',
            'title'       => 'required|string|max:255',
            'question'     => 'required|array',
            'question.*'   => 'required|string|max:500',
            'status'      => 'required|in:0,1',
        ]);

        Appraisal::create([
            'class_id'   => $request->class_id,
            'teacher_id' => json_encode($request->teacher_id),
            'ans_type'   => $request->ans_type,
            'title'      => $request->title,
            'question'   => json_encode($request->question),
            'status'     => $request->status,
            'school_id'     => auth()->user()->school_id
        ]);

        return redirect()->back()->with('message', 'You have successfully created qustions.');
    }

    public function appraisalQustionEdit($id)
    {
        $appraisal = Appraisal::find($id);
        $classes = Classes::get()->where('school_id', auth()->user()->school_id);
        $teachers = User::get()->where('role_id', 3)->where('school_id', auth()->user()->school_id);
        return view('admin.appraisal.appraisalQustionEdit', ['classes' => $classes, 'appraisal' => $appraisal, 'teachers' => $teachers]);
    }

    public function appraisalQustionUpdate(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required',
            'teacher_id' => 'required|array',
            'ans_type' => 'required',
            'title' => 'required|string',
            'question' => 'required|array',
            'status' => 'required|in:0,1',
        ]);

        $appraisal = Appraisal::find($id);

        $appraisal->class_id = $request->input('class_id');
        $appraisal->teacher_id = json_encode($request->input('teacher_id'));
        $appraisal->ans_type = $request->input('ans_type');
        $appraisal->title = $request->input('title');
        $appraisal->question = json_encode($request->input('question'));
        $appraisal->status = $request->input('status');

        $appraisal->save();

        return redirect()->back()->with('message', 'You have successfully updated.');
    }

    public function appraisalQustionDelete($id)
    {
        Appraisal::where('id', $id)->delete();
        return redirect()->back()->with('message', 'Delete successfully.');
    }

    public function appraisalFeedback()
    {
        $feedbacks = Appraisal_submit::where('school_id', auth()->user()->school_id)->get();

        $teacher_ids = [];
        foreach ($feedbacks as $feedback) {
            $decodedAnswers = json_decode($feedback->answers, true);
            $teacher_ids = array_merge($teacher_ids, array_keys($decodedAnswers));
        }
        $teacher_ids = array_unique($teacher_ids);

        $teachers = DB::table('users')->whereIn('id', $teacher_ids)->pluck('name', 'id');

        $appraisals = DB::table('appraisals')->pluck('title', 'id');

        $students = DB::table('users')->whereIn('id', $feedbacks->pluck('student_id'))->pluck('name', 'id');

        return view('admin.appraisal.studentFeedback', compact('feedbacks', 'teachers', 'appraisals', 'students'));
    }
}
