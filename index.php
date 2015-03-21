<!doctype html>
<meta charset="utf-8">
<title>Fetching alarm state… — Rotary Pub</title>
<link rel="stylesheet" href="css/index.min.css">
<link rel="stylesheet" href="/vendor/css/font-awesome-4.3.0/css/font-awesome.min.css">

<body id="alarm-status-container">

<a href="..">
  <p><i id="alarm-status-icon" class="fa fa-pulse fa-spinner"></i> <span id="alarm-status-text" class="alarm-status-text">Fetching alarm state…</span>
  <p id="alarm-status-time"><span id="alarm-status-time"></span></p>
</a>

<script src="js/APIRequest/APIRequest.min.js"></script>
<script src="js/updateAlarmStatus/updateAlarmStatus.js"></script>
<script>
(function updateAlarmStatusClosure() {
  var alarm_status_api_url = 'api_current_status.php';
  var title_field = 'short_title';
  var interval = 5*60*1000;

  function updateTitle(as) { document.title = as.short_title + " — Rotary Pub"; }

  updateAlarmStatus(alarm_status_api_url, title_field, interval, updateTitle);
})();
</script>
