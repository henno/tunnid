$(document).ready(function(){
	var group = '';
	var lessontype = '';
	var subject = '';
	var date_from = '';
	var date_to = '';
	var updating = false;

	var dataUrl = 'data-new.php';
	
	detectVars();

	$(window).bind('beforeunload', function(){
		if (updating)
			return 'Andmebaasi värskendamine käib veel';
	});

	$(window).on('scroll', function(){
		tbl = $('#maintable').offset().top;
		wst = $(window).scrollTop();
		if (wst > tbl && ($('.process-buttons').css('position') != 'fixed')) {
			$('.process-buttons').css({position: 'fixed', left: $('#maintable').offset().left - 55, top: '5px'});
		} else if (wst < tbl && $('.process-buttons').css('position') == 'fixed') {
			$('.process-buttons').css({position: 'absolute', left: -40, top: 0});
		}
	});


// CHECKBOXES
	$('#maintable').on('change', '#checkAll', function(){
		$('#maintable .lessonrow:visible .rowcheck, #maintable .daterow:visible .datecheck').prop('checked', $('#checkAll').prop('checked'));
		processBtnToggle();
	});
	$('#maintable').on('change', '.datecheck', function(){
		$(this).closest('.daterow').nextUntil('.daterow').find('.rowcheck:visible').prop('checked', $(this).prop('checked'));
		processBtnToggle();
	});
	$('#maintable').on('click', '.rowcheck', function(){
		processBtnToggle();
	});


	$('.processed-apply').click(function(){
		var tp = {};
		$('.rowcheck:checked').closest('tr').each(function(i){
			$(this).addClass('processed');
			tp[i] = $(this).data('id');
		});
		$.ajax({
			type: 'POST',
			url: 'process.php?add',
			beforeSend: function() {
				$('.loader').show();
			},
			data: tp,
		}).done(function(){
			$('.loader').hide();
		});
		$('input[type="checkbox"]').prop('checked', false);
		processBtnToggle();
		return false;
	});

	$('.processed-remove').click(function(){
		var tp = {};
		$('.rowcheck:checked').closest('tr').each(function(i){
			$(this).removeClass('processed');
			tp[i] = $(this).data('id');
		});
		$.ajax({
			type: 'POST',
			url: 'process.php?remove',
			beforeSend: function() {
				$('.loader').show();
			},
			data: tp,
		}).done(function(){
			$('.loader').hide();
		});
		$('input[type="checkbox"]').prop('checked', false);
		processBtnToggle();
		return false;
	});

	var today = new Date();
	var thisDate = today.getDate()+'.'+(today.getMonth()+1)+'.'+today.getFullYear();

// SELECTORS
	$('#group-selector').on('change', function(){
		group = $(this).val();
		filter();
	});

	$('#lessontype').on('change', function(){
		lessontype = $(this).val();
		filter();
	});
	$('#subject-selector').on('change', function(){
		subject = $(this).val();
		filter();
	});

// REMOVE FILTERS
	$('#remove-filters').on('click', function(){
		group = '';
		lessontype = '';
		subject = '';
		filter();
		$('.selector option:first-child').attr("selected", "selected");

		return false;
	});

	$('#update-database').on('click', function(){
		updateDatabase();
		return false;
	});

/*	$('#date_from').datepicker({
		dateFormat: "dd.mm.yy",
		firstDay: 1,
		beforeShowDay: $.datepicker.noWeekends,
		onClose: function( selectedDate ) {
			$( "#date_to" ).datepicker( "option", "minDate", selectedDate );
			updateData();
		}
	});
	$('#date_to').datepicker({
		dateFormat: "dd.mm.yy",
		firstDay: 1,
		beforeShowDay: $.datepicker.noWeekends,
		onClose: function( selectedDate ) {
			if($('#date_from').val() == '') {
				$("#date_from").datepicker( "setDate", thisDate );
			}
			$( "#date_from" ).datepicker( "option", "maxDate", selectedDate);
			updateData();
		}
	});*/

	$('#date_from').datetimepicker({
		  format:'d.m.Y H:i',
		  allowTimes:['08:30', '10:15', '11:55', '14:10', '15:45', '17:20', '18:55', '20:35'],
		  onShow:function( ct ){
		   this.setOptions({
			maxDate:$('#date_to').val()?$('#date_to').val():false
		   })
		  },
		  dayOfWeekStart: 1,
		  onClose: function(){
			updateData();
		  }
	});
	$('#date_to').datetimepicker({
		  format:'d.m.Y H:i',
		  allowTimes:['10:00', '11:45', '14:00', '15:40', '17:15', '18:50', '20:25', '22:05'],
		  onShow:function( ct ){
		   this.setOptions({
			minDate:$('#date_from').val()?$('#date_from').val():false
		   })
		  },
		  dayOfWeekStart: 1,
		  onClose: function(){
			updateData();
		  }
	});

	$('a.period').on('click', function(){
		$('#date_from').val($(this).data('start'));
		$('#date_to').val($(this).data('end'));
		updateData();
		return false;
	});

	function updateData() {
		if ($('#date_from').val() != '' && $('#date_to').val() != '')
		$.ajax({
			url: dataUrl,
			data: ({
				date_from: $('#date_from').val(),
				date_to: $('#date_to').val(),
			}),
			beforeSend: function() {
				date_from = $('#date_from').val();
				date_to = $('#date_to').val();
				// changeUrl();
				$('.loader').show();
			},
			dataType: 'json',
		}).done(function(data){
			$('#maintable tbody').html(data['table']);
			$('#group-selector').html(data['groups']);
			$('#subject-selector').html(data['subjects']);
	
			if (!updating)
				$('.loader').hide();

			processData();
			
			if (group != '')
				$('#group-selector option').filter(':contains('+group+')').attr('selected', 'selected');
			if (subject != '')
				$('#subject-selector option').filter(':contains('+subject+')').attr('selected', 'selected');
			if (lessontype != '')
				$('#lessontype option[value='+lessontype+']').attr('selected', 'selected');

			filter();
		});
	}
	function showHeaders(){
		$('.daterow').show();
		$('.daterow').each(function(){
			if ($(this).nextUntil('.daterow').filter(':visible').length == 0) {
				$(this).hide();
			}
		});
	}

	function processData(){
		$('.lessonrow').not(':contains("arvutiklass")').addClass('bold');
		$('.daterow').each(function(){
			endtime = $(this).nextUntil('.daterow').find('.end-time').last().text();
			$(this).find('.date-end').text(endtime);
		});
	}

	function filter(){
		$('.lessonrow').hide();
		lessons = $('.lessonrow');
		if (lessontype != 'teooria')
			lessons = lessons.filter(':contains("'+lessontype+'")');
		else if (lessontype == 'teooria')
			lessons = lessons.not(':contains("arvutiklass")');

		lessons.filter(':contains("'+group+'")').filter(':contains("'+subject+'")').show();


		$('.lessonrow:visible').each(function(index){
			$(this).find('.count').html(index+1);
		});
		$('.lessonrow').not(':visible').find('.rowcheck').prop('checked', false);
		showHeaders();
		changeUrl();
	}
	function updateDatabase(){
		$.ajax({
			url: 'update-database.php',
			beforeSend: function() {
				svUpdate();
				updating = true;
				$('.loader').show();
			},
		}).done(function(d){
			$('#update-database').attr('title', 'Uuenda andmebaasi ('+d+')');
			$('.last-update').text(d);
			updating = false;
			$('.loader').hide();

			updateData();
		});
	}

	function loadAll(){
		$.ajax({
			url: dataUrl,
			dataType: 'json',
			beforeSend: function() {
				$('.loader').show();
			},
		}).done(function(data){
			$('#maintable tbody').html(data['table']);
			$('#group-selector').html(data['groups']);
			$('#subject-selector').html(data['subjects']);
			$('.loader').hide();
			processData();
		});
	}
	function changeUrl() {
		if ($('#date_from').val() != '') date_from = $('#date_from').val();
		if ($('#date_to').val() != '') date_to = $('#date_to').val();

		udf = (date_from != '' && date_from != undefined)? '&date_from='+date_from : '';
		udt = (date_to != '' && date_to != undefined)? '&date_to='+date_to : '';
		ugr = (group != '' && group != undefined)? '&group='+group : '';
		ult = (lessontype != '' && lessontype != undefined)? '&lessontype='+lessontype : '';
		usu = (subject != '' && subject != undefined)? '&subject='+subject : '';

		urlVars = udf + udt + ugr + ult + usu;
		history.replaceState({}, '', '?'+urlVars.substring(1));
	}
	function detectVars(){
		if ($('.should-update').length > 0) {
			console.log('update database');
			updating = true;
			updateDatabase();
		}
		if (window.location.href.slice(window.location.href.indexOf('?') + 1).length > 1) {
			varPairs = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			if (varPairs.length > 0) {
				varArray = [];
				varPairs.forEach(function(element){
					temp = element.split('=');
					varArray[temp[0]] = decodeURIComponent(temp[1]);
				});

				if ('date_from' in varArray)
					$('#date_from').val(varArray['date_from']);
				if ('date_to' in varArray)
					$('#date_to').val(varArray['date_to']);
				updateData();
				if ('group' in varArray)
					group = varArray['group'];
				if ('lessontype' in varArray)
					lessontype = varArray['lessontype'];
				if ('subject' in varArray)
					subject = varArray['subject'];
			} 
		} else {
			updateData();
		}
	}

	function processBtnToggle(){
		isChecked = ($('input[type="checkbox"]:checked').length > 0)? true : false;

		if (isChecked && $('.process-buttons').not(':visible'))
			$('.process-buttons').show();
		else if (!isChecked && $('.process-buttons').is(':visible'))
			$('.process-buttons').hide();
	}

	
	function svUpdate(){
		$.ajax({
			type: 'POST',
			url: 'sv-update.php',
			beforeSend: function() {
				updating = true;
				$('.loader').show();
			},
		}).done(function(){
			updating = false;
			$('.loader').hide();
			updateData();
		});
	}
	
});