<?php
if(!isset($_SESSION)){
  session_start();
}
?>
Highcharts.theme = {
  colors: ['#acf19e', '#f9ba85', '#9dc8f1', '#28a745', '#c0c0c0', '#e15a5a', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
};
Highcharts.setOptions(Highcharts.theme);
var colors = Highcharts.getOptions().colors;
var param = {};
var metrics_table, rawdata_table;

$(document).ready(function () {
  //plot three bar charts 
  drawStudySummary("RNA-seq", "summary_barchart_rnaseq", colors[3]);
  drawStudySummary("Methylation", "summary_barchart_methylation", colors[8]);
  drawStudySummary("Metabolomics", "summary_barchart_metabolomics", colors[7]);

  //plot line/area chart
  drawProgressOverTime();

  //generate metrics table tabulator 
  metrics_table = new Tabulator("#qc_metrics_table", {
    placeholder: 'No result found.',
    layout: "fitColumns",
    movableRows:true,
    tooltips: true,
    autoColumns:false,
    selectable: 1,
    ajaxURL: '/omics/lib/qc_metrics_table.php',
    ajaxConfig: 'GET',
    pagination: 'remote',
    responsiveLayout: false,
    columns: [
      {rowHandle:true, formatter:"handle", headerSort:false, width:30, minWidth:30},
      {title: 'QC Measures', field: 'full_attribute', headerFilter: "input"},
      {title: 'Box plot', field: 'boxPlot', download: false, headerSort:false, formatter:
        function(cell, formatterParams, onRendered){
          onRendered(function(){
            $(cell.getElement()).sparkline(cell.getValue(), {width:"100%", raw:true, type:"box"});
          });
        },
      },
      {title: 'Mean', field: 'mean' },
      {title: 'SD', field: 'sd'},
      {title: 'Min', field: 'min', visible:false, download: true},
      {title: '25%', field: 'pct25', visible:false, download: true },
      {title: 'Median', field: 'median'},
      {title: '75%', field: 'pct75', visible:false, download: true },
      {title: 'Max', field: 'max', visible:false, download: true },
    ],
    rowClick: function(e, row){
      if($("#study_dropdown").hasClass("hidden")) {
        var opts = studyDropdown();
        $("#sel_study_histogram").append(opts);
        opts = studyDropdown("RNA-seq", "");
        $("#sel_study_two").append(opts);
        $("#study_dropdown").removeClass("hidden");
      }
      param["field"] = row.getData().field_name;
      param["study"] = $("#sel_study_histogram").val();
      param["compare"] = $("#sel_study_two").val();
      param["label"]= row.getData().full_attribute;
      drawHistogram(param);
    },
  });

<?php if(isset($_SESSION['access_token']) AND isset($_SESSION['omics_user'])) { //password protected ?>
  opts = studyDropdown();
  $("#sel_study_table").append(opts);
  rawDataTable();
  
  $("#sel_study_table, #sel_size_table").on('change', function() {
    var study = $("#sel_study_table").val();
    var pageSize = $("#sel_size_table").val(); 
    rawDataTable(study, pageSize);
  });

  //download qc raw data in tsv
  $("#download-table-raw").click(function(){
    rawdata_table.download("csv", "omics-table-raw.tsv",{delimiter:"\t"});
  });
	
<?php } ?>

  //hide pagination footer controls
  $("#qc_metrics_table .tabulator-footer").hide();
 
  //download qc metrics data
  $("#download-table-metrics").click(function(){
    metrics_table.download("csv", "omics-table-metrics.tsv", {delimiter: '\t'});
  });

  $("#sel_study_histogram, #sel_study_two").on('change', function() {
    param['study'] = $("#sel_study_histogram").val(); 
    param['compare'] = $("#sel_study_two").val();
    drawHistogram(param);
  });
 
});

function rawDataTable(study="", pageSize=100) {
  //generate Samples raw table tabulator
  rawdata_table = new Tabulator("#sample_raw_table", {
    placeholder: 'No result found.',
    tooltipsHeader: true,
    layout: "fitColumns",
    //height: "700px",
    ajaxURL: '/omics/lib/sample_raw_qc.php',
    ajaxConfig: 'post',
    pagination: 'remote',
    paginationSize: pageSize,
    ajaxSorting: true,
    ajaxFiltering: true,
    responsiveLayout: true,
    ajaxParams: {"study": study},
    columns: [
      { title: 'Samples', 
        columns: [
          {title: 'id', field: 'id', headerFilter:"input", headerFilterPlaceholder:"Look up sample"},
          {title: 'Study', field: 'study_id'}
        ],
      },
      { title: 'QC measures',
        columns: [
          {title: 'Mapping rate', field: 'mapping_rate' },
          {title: 'Base mismatch', field: 'base_mismatch'},
          {title: 'Unique rate of mapped', field: 'unique_rate_of_mapped'},
          {title: 'Exonic rate', field: 'exonic_rate'},
          {title: 'Intronic rate', field: 'intronic_rate'},
          {title: 'High quality exonic rate', field: 'high_quality_exonic_rate'},
          {title: 'rRNA rate', field: 'rrna_rate'},
          {title: 'rRNA reads', field: 'rrna_reads' },
          {title: 'Total mapped pairs', field: 'total_mapped_pairs' },
        ],
      }
    ],
  });
}

function drawStudySummary(topic="RNA-seq", div, color) {
  //dataset for samples
  var dataset = $.ajax({
    url : "/omics/lib/sample_summary.php?topic="+topic,
    dataType : "json",
    async : false,
  }).responseText;
  var jsonData = JSON.parse(dataset);

  //write summaries in the paragraph below the header
  var sample = "#"+topic+"_samples";
  var study = "#"+topic+"_study";  
  $(sample).html(jsonData["total"]);
  $(study).html(jsonData["category"].length); 

  //set bar chart options
  var options_summary = {
    chart: {
      renderTo: div,
      type: 'bar',
    },
    xAxis: {
      categories: jsonData['category'],
      title: {
        text: 'Studies'
      },
    },
    title: {
      text:  topic
    },
    subtitle: {
      text: jsonData["total"] + ' total samples'
    },
    yAxis : {
      title : {
        text: 'number of samples',
      },
    },
    legend: {
      enabled: false
    },
    plotOptions: {
      series: {
        dataLabels: {
          enabled: true
        },
        animation: false
      }
    },
    credits: {
      enabled: false
    },
    series: [{
      name: 'completed samples',
      data: jsonData['dataset'],
      color: color,
    }]
  };
  var barchart = new Highcharts.Chart(options_summary);  
}

function drawProgressOverTime() {
  var newSeries = [];
  var jsonData;
  var category = [];
  var dataset = $.ajax({
    url : "/omics/lib/progress_overtime.php",
    dataType : "json",
    async : false,
  }).responseText;

  jsonData = JSON.parse(dataset);
  category = jsonData['category'];

  //calculate sum by date
  for(var i=0; i<jsonData['data'].length; i++) {
    if(i==0) {
      newSeries[i] = parseInt(jsonData['data'][i]);
    } else {
      newSeries[i] = newSeries[i-1] + parseInt(jsonData['data'][i]);
    }
  };
  
  //set chart options
  var options_timeline = {
    chart: {
      renderTo: 'timeline_chart',
      type: 'area',
    },
    xAxis: {
      categories: category,
      title: {
        text: 'QC Date'
      },
    },
    title: {
      text:  'RNA-seq progress over time',
      style: {
        color: '#1b809e',
        fontWeight: 'normal',
        fontSize: '1.75rem'
      }
    },
    yAxis : {
      title : {
        text: 'number of samples',
      },
    },
    tooltip: {
      split: true,
    },
    legend: {
      enabled: false
    },
    plotOptions: {
      area: {
        lineWidth: 1,
        dataLabels: {
          enabled: true,
        },
        fillColor: {
          linearGradient: {
            x1: 0,
            y1: 0,
            x2: 0,
            y2: 1
          },
          stops: [
            [0, Highcharts.getOptions().colors[2]],
            [1, Highcharts.color(Highcharts.getOptions().colors[2]).setOpacity(0).get('rgba')]
          ]
        },
        marker: {
          enabled: false,
          symbol: 'circle',
          states: {
            hover: {
              enabled: true
            }
          }
        }
      },
    },
    credits: {
      enabled: false
    },
    series: [{
      name: 'completed samples',
      data: newSeries,
      color: colors[6],
    }]
  };
  var linechart = new Highcharts.chart(options_timeline);
}

function studyDropdown(topic="RNA-seq", firstSel="All") {
  var dataset = $.ajax({
    url : "/omics/lib/sample_summary.php?topic="+topic,
    dataType : "json",
    async : false,
  }).responseText;
  var jsonData = JSON.parse(dataset);
  var studies = jsonData["category"];
  var selection; 
  if(firstSel == "All") {
    selection += '<OPTION value="all">All studies</OPTION>';
  } else {
    selection += '<OPTION value="">Choose one</OPTION>';
  }
  jQuery.each(studies, function(){
    selection += '<OPTION value="'+this+'">'+this+'</OPTION>';
  });
  return selection;
}

function drawHistogram(param) {
  if(param) {
    var json = $.ajax({
      url : "/omics/lib/datasets_by_measure.php",
      data: param,
      dataType : "json",
      async : false,
    }).responseText;

  var jsonColData = JSON.parse(json);
  var subtitle = param['study'] +" ("+jsonColData['data'].length+" samples)";
  subtitle+=(param["compare"])?" vs. "+param["compare"]+" ("+jsonColData["compare"].length+" samples)":"";

  Highcharts.chart('qc_histogram', {
    title: {
      text: jsonColData['label'] 
    },
    subtitle: {
      text: subtitle,		
    },
    xAxis: [{
      title: { text: '' },
      alignTicks: false,
      opposite: true
    }, {
      title: { text: 'QC Value' },
      alignTicks: false,
    }],
    yAxis: [{
      title: { text: '' },
      opposite: true      
    }, {
      title: { text: 'Histogram' },
    }],
    legend:{ enabled:false },
    credits:{ enabled:false },
    plotOptions: {
      histogram: {
        accessibility: {
          pointDescriptionFormatter: function (point) {
            var ix = point.index + 1,
              x1 = point.x.toFixed(3),
              x2 = point.x2.toFixed(3),
              val = point.y;
            return ix + '. ' + x1 + ' to ' + x2 + ', ' + val + '.';
          }
        },
       //animation: false
      }
    },

    series: [{
      name: param["study"],
      type: 'histogram',
      xAxis: 1,
      yAxis: 1,
      baseSeries: 's1',
      zIndex: -1,
      color: colors[7],
    }, {
      name: 'Scatter',
      type: 'scatter',
      data: jsonColData['data'],
      id: 's1',
      marker: {
        radius: 1.5
      },
      visible: false, 
    }, {
      name: param["compare"],
      type: 'histogram',
      //pointWidth: 1,
      xAxis: 1,
      yAxis: 1,
      baseSeries: 's2',
      zIndex: -1,
      color: 'rgba(162,20,47,0.60)'
    }, {
      type: 'scatter',
      data: jsonColData['compare'],
      id: 's2',
      visible: false
    }]
   });
  };	
}


