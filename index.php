<?php
require('config.php');

$arnold = file_get_contents("http://$PLEXPY_URL/api/v2?apikey=$PLEXPY_API&cmd=arnold");

$arnold_decode = json_decode($arnold);

$show_donate = strlen($DONATE_URL.$PAYPAL_BUTTON_ID) > 0;

if (strlen($PLEXPY_API)) {
	$libraries = file_get_contents("http://$PLEXPY_URL/api/v2?apikey=$PLEXPY_API&cmd=get_libraries");
	$activity = file_get_contents("http://$PLEXPY_URL/api/v2?apikey=$PLEXPY_API&cmd=get_activity");

	if ($libraries && $libraries = json_decode($libraries)) {
		$libraries = $libraries->response->data;
		if (count($libraries)) {
			$MOVIE_LIBS = explode(',', $MOVIE_LIBS);
			$TV_LIBS = explode(',', $TV_LIBS);
			$tmp_movie_count = 0;
			$tmp_tv_count = 0;

			foreach ($libraries as $lib) {
				if (in_array($lib->section_name, $MOVIE_LIBS)) {
					$tmp_movie_count += 1*$lib->count;
				} elseif (in_array($lib->section_name, $TV_LIBS)) {
					$tmp_tv_count += 1*$lib->count;
				}
			}

			if ($tmp_movie_count > 0) {
				$MOVIE_COUNT = $tmp_movie_count;
			}
			if ($tmp_tv_count > 0) {
				$TV_COUNT = $tmp_tv_count;
			}
		}
	}

	if ($activity && $activity = json_decode($activity)) {
		$STREAM_COUNT = $activity->response->data->stream_count;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Fullcalander -->

<!-- Loading these now as we will need some of them in the coming scripts -->
	<link rel='stylesheet' href='assets/css/fullcalendar.min.css' />
	<script src='assets/lib/jquery.min.js'></script>
	<script src='assets/lib/moment.min.js'></script>
	<script src='assets/js/fullcalendar.min.js'></script>
	<script type='text/javascript' src='assets/js/gcal.min.js'></script>

	<link rel="shortcut icon" type="image/x-icon" href="plexlanding.ico" />`
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<style>
		body.offline #link-bar {
			display:none;
		}

		body.online  #link-bar{
			display:block;
		}
	</style>
	<script src="assets/js/ping.js"></script>
	<script type='text/javascript'>

		HTMLElement.prototype.hasClass = function (className) {
			if (this.classList) {
				return this.classList.contains(className);
			} else {
				return (-1 < this.className.indexOf(className));
			}
		};

		HTMLElement.prototype.addClass = function (className) {
			if (this.classList) {
				this.classList.add(className);
			} else if (!this.hasClass(className)) {
				var classes = this.className.split(" ");
				classes.push(className);
				this.className = classes.join(" ");
			}
			return this;
		};

		HTMLElement.prototype.removeClass = function (className) {
			if (this.classList) {
				this.classList.remove(className);
			} else {
				var classes = this.className.split(" ");
				classes.splice(classes.indexOf(className), 1);
				this.className = classes.join(" ");
			}
			return this;
		};

		function checkServer() {
			var p = new Ping();
			var server = "$PLEX_SERVER";
      		var timeout = 2000; //Milliseconds
      		var body = document.getElementsByTagName("body")[0];
      		var serverMsg = document.getElementById("server-status-msg");
      		var serverImg = document.getElementById("server-status-img");
      		var stream_count = <?=isset($STREAM_COUNT)?$STREAM_COUNT:'null'?>;


      		function serverUp() {
      			serverImg.src = "assets/img/up.svg";
      			body.addClass('online').removeClass("offline");

      			if (stream_count == null) {
      				serverMsg.innerHTML = 'Ready for streaming';
      			} else {
      				serverMsg.innerHTML = 'Currently streaming to <strong>'+
      				stream_count + '</strong> user' + (stream_count == 1 ? '' : 's');
      			}
      		}

      		function serverDown() {
      			serverMsg.innerHTML = 'Down and unreachable';
      			serverImg.src = "assets/img/down.svg";
      		}

      		if (stream_count == null) {
      			p.ping(server, function(data) {
      				if (data < 1000) {
      					serverUp(stream_count);
      				} else {
      					serverDown();
      				}
      			}, timeout);
      		} else {
      			serverUp(stream_count);
      		}
      	}


  <?php if (strlen($GOOGLE_CALENDAR_ID) && (strlen($GOOGLE_CALENDAR_API_KEY))> 0) { ?>
	// Fullcalendar.io
	$(document).ready(function() {
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay,listWeek'
			},

			// views: {
			// 	listDay: { buttonText: 'list day' },
			// 	listWeek: { buttonText: 'list week' }
			// },

			weekNumbers: 'true',
			googleCalendarApiKey: '<?=$GOOGLE_CALENDAR_API_KEY?>',
			navLinks: true,
			contentHeight: 'auto',
			timeFormat: 'H(:mm)', //Change to hh:mm for 12 hour clock

			events: {
				googleCalendarId: '<?=$GOOGLE_CALENDAR_ID?>'
			},

			eventRender: function(event, element) { //Show small tooltip with information
				start=moment(event.start).format('H:mm');
				end=moment(event.end).format('H:mm');
				$(element).tooltip({title: start + " - " + end + "<br>" + event.title, html: true});
			},

			eventClick: function(event) { //Make events not clickable, remove this function if you want to be able to click events.
				if (event.url) {
					return false;
				}
			}
		});
	});
	<?php } ?>
</script>

<title>
	<?=ucfirst($SERVER_NAME)?>
</title>

<!-- Bootstrap core CSS -->
<!-- <link href="assets/css/bootstrap.css" rel="stylesheet"> -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>



<!-- Custom styles -->
<link href="assets/css/main.css" rel="stylesheet">
<!-- Fonts from Google Fonts -->
<link href='//fonts.googleapis.com/css?family=Lato:300,400,900' rel='stylesheet' type='text/css'>


</head>

<body onload="checkServer()" class="offline">
	<!-- Fixed navbar -->
	<div class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header"></div>
			<a class="navbar-brand"><b>Plex</b></a>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#" data-toggle="modal" data-target="#calendarModal">TV Show Calendar</a></li>
			</div>
		</div>
		<!-- /row -->

		<!-- Modal -->
		<div id="calendarModal" class="modal fade" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"></button>
						<h4 class="modal-title">TV Show Calendar</h4>
					</div>
					<div id="calendar" class="modal-body">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Now render the calendar -->
		<script type="text/javascript">
			$('#calendarModal').on('shown.bs.modal', function () {
				$("#calendar").fullCalendar('render');
			});

		</script>

	</div><!--/.nav-collapse -->
</div>
<div class="container" id="link-bar">
	<div class="row mt centered">
		<div class="col-lg-4">
			<a href="https://app.plex.tv/web/app" target="_blank">
				<img src="assets/img/plex.svg" height="180" width="300" alt="">

				<h4>Access Plex</h4>
				<p>Access the Plex library with over <strong><?=$MOVIE_COUNT?></strong> Movies & <strong><?=$TV_COUNT?></strong> TV Shows available instantly.<p>
				</a>
			</div><!--/col-lg-4 -->

			<div class="col-lg-4">
				<a href="//<?=$PLEX_REQUESTS?>" target="_blank">
					<img src="assets/img/request.svg" width="180" alt="">
					<h4>Request</h4>
					<p>Want to watch a Movie or TV Show but it's not currently on Plex? Request it here!</p>
				</a>
			</div><!--/col-lg-4 -->

			<!-- Slack Team -->
			<div class="col-lg-4">
				<?php if (strlen($SLACK_URL) > 0) { ?>
				<a href="//<?=$SLACK_URL?>" target="_blank">
					<img src="assets/img/slack.svg" width="180" alt="">
					<h4>Slack Team</h4>
					<p>Alerts, Requests, and General Chat. Join the Plex Slack Team today!</p>
				</a>
				<?php } elseif ($SLACK_DESATURATE == True) { ?>
				<img src="assets/img/Slack.svg" width="180" class="desaturate" alt="">
				<h4>Slack Team, coming soon!</h4>
				<?php } ?>
			</div>
		</div><!-- /row -->
	</div><!-- /container -->

	<div class="container" id="link-bar">
		<div class="row">
			<div class="col-md-12">
				<h3 class="centered italic" >"<?php echo $arnold_decode->response->data ;?>" - Arnold</h3>
			</div>
		</div>
	</div>

	<div class="container" id="link-bar">
		<div class="row mt centered">

			<div class="col-lg-4">
				<a href="//<?=$PLEXPY_URL?>" target="_blank">
					<img src="assets/img/pie-chart.svg" width="180" alt="">
					<h4>Viewing stats</h4>
					<p>Watched, Viewing, and other stats. Login with your Plex account.</p>
				</a>
			</div><!--/col-lg-4 -->

			<!-- Paypal button -->
			<div class="col-lg-4">
				<?php if ($show_donate) { ?>
				<?php if (strlen($PAYPAL_BUTTON_ID) > 0) { ?>
				<form id="donate_form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input name="cmd" type="hidden" value="_s-xclick" />
					<input name="hosted_button_id" type="hidden" value="<?=$PAYPAL_BUTTON_ID?>" />
					<a href="#" onclick="donate_form.submit();return false">
						<?php } else { ?>
						<a href="http://dereferer.org/?<?=$DONATE_URL?>" target="_blank">
							<?php } ?>
							<img src="assets/img/donate.svg" width="180" alt="">
							<h4>Donate</h4>
							<p>Say thanks and help cover the monthly costs of keeping <?=$SERVER_NAME?> running!</p>
						</a>
					</form>
					<?php } ?>
				</div>
				<!--/col-lg-4 -->


				<div class="col-lg-4">
					<?php if (strlen($SERVER_STATS_URL) > 0) { ?>
					<a href="//<?=$SERVER_STATS_URL?>" target="_blank">
						<img src="assets/img/server.svg" width="180" alt="">
						<h4>Server Stats</h4>
						<p>Graphical Server Information Including CPU, Network, Harddrive and Memory.</p>
						<?php } elseif ($SERVER_STATS_DESATURATE == True) { ?>
						<img src="assets/img/server.svg" width="180" class="desaturate" alt="">
						<h4>Server stats, coming soon!</h4>
						<?php } ?>
					</a>
				</div><!--/col-lg-4 -->
			</div><!-- /row -->
		</div><!-- /container -->
		<p>

			<div id="headerwrap">
				<div class="container">
					<div class="row">
						<div class="col-lg-6">
							<h1><br/>
								<center>Plex Status:</h1></center>
								<center><h4 id="server-status-msg"><img src="assets/img/puff.svg">   Checking...</h4></center><br/>
								<br/>
								<br/>
								<form class="form-inline" role="form">
									<div class="form-group">

									</div>

								</div><!-- /col-lg-6 -->
								<div class="col-lg-6">
									<center><img id="server-status-img" class="img-responsive" src="assets/img/refresh.svg" alt=""></center>
								</div><!-- /col-lg-6 -->

							</div><!-- /row -->
						</div><!-- /container -->
					</div><!-- /headerwrap -->
				</body>
				</html>
