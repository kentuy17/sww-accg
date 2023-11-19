<?php

namespace App\Http\Controllers;

use App\Models\DerbyEvent;
use App\Models\Expenses;
use App\Models\Fight;
use App\Models\User;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('dashboard');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function getBetSummaryByDate(Request $request)
    {
        $start_of_event = Carbon::createFromFormat('Y-m-d', '2023-10-13');
        if ($request->event_date < $start_of_event) {
            return DataTables::of([])
                ->addIndexColumn()
                ->make(true);
        }

        $event = DerbyEvent::whereDate('schedule_date', $request->event_date)->get();

        $fights = Fight::whereIn('event_id', $event->pluck('id'))
            ->with('event')
            ->withSum('bet_legit_meron', 'amount')
            ->withSum('bet_legit_wala', 'amount')
            ->orderBy('fight_no', 'asc')
            ->get();

        return DataTables::of($fights)
            ->addIndexColumn()
            ->make(true);
    }

    public function deposit()
    {
        return view('deposit');
    }

    public function getDepositData()
    {
        $trans = Transactions::where('action', 'deposit')
            ->whereIn('morph', [0, 2])
            ->with('user')
            ->with('operator')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($trans)
            ->addIndexColumn()
            ->make(true);
    }

    public function withdraw()
    {
        return view('withdraw');
    }

    public function getWithdrawData()
    {
        $trans = Transactions::where('action', 'withdraw')
            ->with('user')
            ->with('operator')
            ->where('deleted', false)
            ->whereIn('morph', [0, 2])
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($trans)
            ->addIndexColumn()
            ->with('pending_count', $trans->where('status', 'pending')->count())
            ->toJson();
    }

    public function expenses()
    {
        return view('expenses');
    }

    public function getExpensesData(Request $request)
    {
        $expenses = Expenses::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($expenses)
            ->with('auditor', Auth::user()->email)
            ->addIndexColumn()
            ->toJson();
    }

    public function addExpenses(Request $request)
    {
        $file = $request->file('attachment');
        if ($file && $file->getSize() < 1000) {
            // return response()->json([
            //     'message' => 'Invalid attachment',
            //     'status' => 'error'
            // ], 400);
            return redirect()->back()->with('error', 'Invalid attachment');
        }

        if ($file) {
            $imageName = time() . '.' . $request->attachment->extension();
            $path = 'public/' . $imageName;
            Storage::disk('local')->put($path, file_get_contents($request->attachment));
        }

        // dd($request->all());

        // $post_date = Carbon::createFromFormat('d/m/Y', $request->posting_date);
        $post_date = date('Y-m-d', strtotime($request->posting_date));

        Expenses::create([
            'added_by' => Auth::user()->id,
            'name' => $request->name,
            'amount' => $request->amount,
            'attachment' => $imageName ?? '',
            'post_date' => $post_date,
            'note' => $request->note,
        ]);

        // return response()->json([
        //     'data' => $request->all(),
        // ]);
        return redirect()->back()->with('success', 'Record added successfully');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
