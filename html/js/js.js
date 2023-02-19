var attr = '';
var interval = null;
var refreshAfterAjax = null;

String.prototype.replaceAll = function(search, replacement) {
	var target = this;
	return target.replace(new RegExp(search, 'g'), replacement);
};


	function download(filename, text) {
	var element = document.createElement('a');
	element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
	element.setAttribute('download', filename);

	element.style.display = 'none';
	document.body.appendChild(element);

	element.click();

	document.body.removeChild(element);
}

function googleCharts(chartPopupDiv, thisElement, timestamp ){
	chartPopupDiv.draggable();
	chartPopupDiv.slideToggle("fast");
	var valueAttribute = "";
	valueAttribute = thisElement.attr("value");
	var popupChartData = [];
	var popupChartDataX = [];
	var popupChartDataY = [];
	if(valueAttribute != "name"){
		$("."+valueAttribute).each(function() {
			$.each(this.attributes,function(i,a){
				if(a.name=="value"){
					popupChartDataX.push(a.value);
				}
			})
		});
	}
	var previousDate = null;
	var j = 0;
	timestamp.each(function() {
		$.each(this.attributes,function(i,a){
			if(a.name=="value"){
				if(previousDate != null){
					var currDate = new Date(a.value.substring(0, 10));
					var days = ((previousDate.getTime() - currDate.getTime())/(24*60*60*1000)-1);
					for(i = 0; i < days; i++){
						var newTmpDate = new Date(a.value);
						newTmpDate = new Date(newTmpDate.getTime()+(24*60*60*1000)*(i+1));
						var newTmpDateStr = newTmpDate.getFullYear()+"-"+(newTmpDate.getMonth()+1)+"-"+newTmpDate.getDate()+" 00:00:00"
						newTmpDateStr.replace(/(^|\D)(\d)(?!\d)/g, "$10$2");
						popupChartDataY.push(newTmpDateStr);
						if(valueAttribute == "name"){
							popupChartDataX.push(0);
						}
					}
				}
				popupChartDataY.push(a.value);
				previousDate = new Date(a.value.substring(0, 10));

				if(valueAttribute == "name"){
					popupChartDataX.push($("."+valueAttribute)[j].innerHTML);
				}
				j++;
			}
		})
	});
	logConsole(popupChartDataX);
	popupChartDataX.reverse();
	popupChartDataY.reverse();

	var maxValue = popupChartDataX.reduce(function(a, b) {
		return Math.max(a, b);
	});
	var minValue = popupChartDataX.reduce(function(a, b) {
		return Math.min(a, b);
	});
	if(minValue>0)minValue = 0;

	var index;
	for (index = 0; index < popupChartDataX.length; ++index) {
		var date = popupChartDataY[index];
		var value = Number(popupChartDataX[index]);
		popupChartData.push([{v: date, f: date}, value]);
	}

	google.charts.load('current', {packages: ['corechart', 'bar']});
	google.charts.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'text');
		data.addColumn('number', 'value');
		data.addRows(popupChartData);
		var options = {
			legend: {position: 'none'},
			height: 430,
			width: 1000,
			chartArea: {width: "90%", height: "95%"},
			vAxis: {
				viewWindowMode:'explicit',
				viewWindow: {
					max:maxValue,
					min:minValue
				}
			},
		};
		var chart = new google.visualization.ColumnChart(document.getElementById('chart-popup-div-container'));
		chart.draw(data, options);
	}
}
function urlEncode(txt) {	
	txt = txt.replaceAll('&', '%26');
	txt = txt.replaceAll('\\+', '%2b');
	// txt = txt.replaceAll('/', '.');
	return txt;
}

function logConsole(text) {
	let timestamp = new Date();
	var new_item = $('<a href="#" class="dropdown-item"><div class="media"><div class="media-body"><span class="text-xs"> ' + text + '<span class="float-right"></span></span><p class="text-xs"></p><p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> ' + timestamp.toLocaleDateString() + " " + timestamp.toLocaleTimeString() + '</p></div></div></a><div class="dropdown-divider"></div>'
	).hide();
	$('.NotificationsMenu').append(new_item);
	new_item.show('normal');
	
	var count = $('.NotificationsMenuCount').text();
	if(count == ''){count = 0;}

	$('.NotificationsMenuCount').text((parseInt(count) + 1));
	$('.NotificationsMenuCountText').text((parseInt(count) + 1)+" Notifications");
	
	console.log(text);
}

function ajax(attr, doConfirm = false, formData = null){
	var doContinue = true;
	if(doConfirm){
		if(!confirm("Are you sure?")) {
			doContinue = false;
		}
	}
		
	if(doContinue){
		if(formData == null){
			formData = new FormData();
		}
		$.ajax({
			url: (attr),
			type: "POST",
			data : formData,
			processData: false,
			contentType: false,
			context: document.body
		}).done(function(data) {
			logConsole(attr);
			//logConsole(formData);
			logConsole(data);
			return data;
		});
	}
}
 

function getLastImageNameForCamera(location, camId, time, camElementId, timer, button){
	var attr = "?a=getLastImageForCamera&location="+location+"&camId="+camId+"&time="+time;	
	$.ajax({url: attr,context: document.body})
	.done(function(data) {
		if(data.length > 0){		
			$(camElementId).attr("src", "files/cam/"+location+'/'+data);
			$(button).removeClass("loading");
			$(button).addClass("refresh");	

			clearInterval(timer);
		}
	});
}