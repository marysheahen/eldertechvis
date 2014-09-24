/* Globals... */

var mu_key = [];

var c_sid = 0;
var c_swin = 0;
var init_data = [];
var new_init = [];
var init_walkData = [];
var init_hs_mpd = [];
var init_sl_mpd = [];
var init_st_mpd = [];
var init_hs_data = [];
var init_sl_data = [];
var init_st_data = [];
var init_rids = [];
var init_heights = [];
var init_h_plot = [];
var init_K = 0;

var gaitData = [];
var alertsToPlot = [];
var paramGraph;
var riskGraph;
var pcolors = ["rgb(30,30,30)","rgb(60,60,255)","rgb(203,75,75)","rgb(77,167,77)","rgb(50,50,50)"];
var msecDay = 24*60*60*1000;

var TUGmodel = [49.202, 13.0033, 1.660306731, 0.824959545, 67.52687779, -54.13014449];

var mw_options = [30, 60, 90];
var mw_text = ["30 days", "60 days", "90 days"];
var dw_options = [7, 14];
var dw_text = ["7 days", "14 days"];
var sm_text = ["low", "medium", "high"];

var id_options = [];
var sid_options = [];
var id_text = [];

var rid_all = [];      // Includes all users from user table...
var height_all = [];   
var sid_all = [];  //Includes all systems, not just those with data in gaitTable
var sid_text = [];
var swin_options = [7,14,28,56,84];
var swin_text = ["7 days", "14 days", "28 days", "56 days", "84 days"];

var dStart = 500*365*msecDay;   //way in the future...
var dEnd = 0;

var dsStart = -1;
var dsEnd = -1;
var mDate = -1;

var c_id = 0;
var c_mw = 0;
var c_dw = 0;
var c_sm = 0;
var previousPoint = -1;

var nWin = 28;   // 4 week window for baseline
var cWin =  7;   // 1 week window for current value
var hY = [0.035, 0.015];   // Change percentage
var rD = 0.5;    // Required data percentage
var fTu = [3, 4, 5];  //Features to use...

var modelDataString = [];
var mParams = [];
var objStringToSave = [];

