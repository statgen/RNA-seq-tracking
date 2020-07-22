<?php
if(!isset($_SESSION)){ 
  session_start();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>TOPMed OMICS Progress Report</title>
    
    <!-- Bootstrap CSS -->
    <link href="../report/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="data:text/css;charset=utf-8," data-href="../report/dist/css/bootstrap-theme.min.css" rel="stylesheet" id="bs-theme-stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tabulator/4.4.3/css/tabulator.min.css" rel="stylesheet">
    <link href="../report/css/docs.css" rel="stylesheet">
    <link href="../report/css/template.css" rel="stylesheet">
    <!-- jquery ui js and css for autocomplete -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.css" rel="stylesheet"/>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fetch/2.0.4/fetch.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script type="text/javascript" src="https://code.highcharts.com/stock/highstock.js"></script>
    <script type="text/javascript" src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/histogram-bellcurve.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script type="text/javascript" src="https://code.highcharts.com/modules/exporting.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tabulator/4.4.3/js/tabulator.min.js"></script>
    <script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
  </head>
  <body>
    <style>
      .container {width: 95%}
      div.loading { background-image: url("images/ajax-loader.gif") }
      .bs-docs-header { background-image: linear-gradient(to bottom, #0f463c 0, #185f65 100%); }
      .bs-docs-header h1 { margin-right: auto }
      .bs-docs-footer { height: 30px; margin: 20px 0; padding: 40px 0; background-color: #0f463c; color: #fff;}
      .chartheader { text-align: center; color: #1b809e }
      .row { padding: 15px; margin: 40px 0 }
      p.summary {font-size: 16px;}
      p.center {text-align: center }
      .right {float: right }
      .bs-callout-info h3 { font-weight: normal }
      #summary_barchart_rnaseq, #summary_barchart_methylation, #summary_barchart_metabolomics, #timeline_chart, #qc_histogram 
      { height: 420px; width: 100% } 
      .bs-docs-nav .navbar-brand, .bs-docs-nav .navbar-nav>li>a { background: white; }
    </style>
    <header class="navbar navbar-static-top bs-docs-nav" id="top" role="banner">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">TOPMed IRC</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href ="#">OMICS</a></li>  
            <li><a href="about.php">About</a></li>   
            <li><a href="../report/">Whole genomes</a></li>
          </ul>
        </div>
      </div>
    </header>

    <div class="bs-docs-header" id="content" tabindex="-1">
      <div class="container">
        <h1 id="subject">TOPMed OMICS Progress Report</h1>
        <h4><strong>Last updated</strong> : <?=date("m/d/Y");?></h4>
         <p class="lead">This page reports TOPMed Omics data received by the IRC.
        </p>
      </div>
    </div>

    <div class="container bs-docs-container">
      <div class="bs-callout bs-callout-info">
        <h3 id="progress">OMICS sample numbers by study</h3>
      </div>
      <div class="row loading">
        <div class="col-md-4 col-sm-12">
          <h3 class="chartheader">RNA-seq</h3>
          <div id="summary_barchart_rnaseq"> </div>
        </div>
        <div class="col-md-4 col-sm-12">
          <h3 class="chartheader">Methylation</h3>
          <div id="summary_barchart_methylation"> </div>
        </div>
        <div class="col-md-4 col-sm-12">
          <h3 class="chartheader">Metabolomics</h3>
          <div id="summary_barchart_metabolomics"> </div>
        </div>
      </div>
      <div class="bs-callout bs-callout-info">
        <h3>RNA-seq progress over time</h3>
      </div>
      <div class="row loading" id="timeline_div">
        <div class="col-lg-12">
          <div id="timeline_chart"></div>  
        </div>
      </div>
      <div class="row loading">
        <div class="col-md-7 col-sm-12">
          <h3 class="chartheader">RNA-seq QC metrics</h3> 
          <p>This table shows aggregates across studies for many metrics of RNA-seq quality. &emsp; &emsp; <button id="download-table-metrics" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-list-alt"></span> &nbsp; Export table</button></p> 
          <div>
            <div id="qc_metrics_table" style="height: 420px; overflow-y: hidden; overflow-x: hidden"> </div>
          </div> 
        </div>
        <div class="col-md-5 col-sm-12">
          <h3 class="chartheader">Histogram of QC Measure</h3>
            <p id="study_dropdown" class="center hidden">
              <label for="sel_study_histogram">1st study: <select id="sel_study_histogram"></select></label> &emsp; 
              <label for="sel_study_two">2nd study: <select id="sel_study_two"></select></label>
            </p>  
            <div id="qc_histogram"><p class="lead center" style="min-height:420px">Cilck rows in the QC metrics table</p></div>
        </div>
      </div>
      <div class="bs-callout bs-callout-info">
        <h3>Per sample RNA-seq quality metrics</h3>
        <p class="lead">Please log in using Google to download individual level QC results. Email tblackw@umich.edu if you aren't already on the whole genome sequence whitelist.</p>
<?php if(isset($_SESSION['access_token']) AND isset($_SESSION['omics_user'])) { ?>
	<a href="?logout" class="btn btn-warning"><span class="glyphicon glyphicon-lock"></span> Logout <?=$user->email; ?></a>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <p class="summary">
            <label for="sel_study_table">Filter by study: <select id="sel_study_table"></select></label> &emsp; &emsp;
            <label for="sel_size_table">Page size: <select id="sel_size_table">
              <option value="100">100</option>
              <?php
	        for($i=1; $i<9; $i++) {
                  print "<option value='".($i*500)."'>".($i*500)."</option>";
                }	
              ?>
            </select></label> &emsp; &emsp; 
            <a id="download-whole-raw" href="lib/datadump.php" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-download-alt"></span> &nbsp; Download entire QC raw data</a> &emsp; 
          </p>
          <div id="sample_raw_table" style="height: 600px; overflow-y: hidden;"></div>
        </div>
       </div>
<?php } else { ?>
        <a class="login" href="<?=$authUrl; ?>"><img src="images/google-login-button.png"/></a>
      </div>
      <div class="row"></div>
<?php } ?>
      <div class="bs-callout bs-callout-info">
        <h3>Summary of OMICS samples received</h3>
      </div>
      <div class="row">
        <p class="summary">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col"></th>
              <th>RNA-seq</td>
              <th>Methylation</td>
              <th>Metabolomics</td>
              <th>Total by Center</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th scope="row">Broad</th>
              <td id="broad-rna-seq"> - </td>
              <td id ="broad-methylation"> - </td>
              <td id="broad-metabolomics"> - </td>
              <td id="broad-total"> - </td>
            </tr>
            <tr>
              <th scope="row">NWGC</th>
              <td id="nwgc-rna-seq"> - </td>
              <td id="nwgc-methylation"> - </td>
              <td id="nwgc-metabolomics"> - </td>
              <td id="nwgc-total"> - </td>
            </tr>
            <tr>
              <th scope="row">USC</th>
              <td id="usc-rna-seq"> - </td>
              <td id="usc-methylation"> - </td>
              <td id="usc-metabolomics"> - </td>
              <td id="usc-total"> - </td>
            </tr>
            <tr>
              <th scope="row">Total by Type</th>
              <td id="total-rna-seq"> - </td>
              <td id="total-methylation"> - </td>
              <td id="total-metabolomics"> - </td>
              <td id="total-all"></td>
            </tr>
          </tbody>
        </table>
      </p>
      <p> &nbsp; </p>
      <p class="summary">
        <strong>Technical:</strong><br/>
        Stranded, poly-A-selected RNA, 101 bp paired end NovaSeq, target 75 M reads per sample for blood samples, 50 M reads per sample for tissue, cell sorted or globin-depleted blood samples.<br/>
        Illumina EPIC methylation array, ~850 k sites.<br/>
        Four metabolomics assay types:  C8-pos, C-18-neg, HILIC-pos (all untargeted), plus Amide-neg with multiple reaction monitoring for ~200 target metabolites.
      </p>
      </div>
    </div>
    <footer class="bs-docs-footer"> 
      <div class="container"> 
        <p>&copy; <?=date("Y"); ?> All Rights Reserved </p>
      </div> 
    </footer>

    <script type="text/javascript" src="js/main.js.php"></script>

    <script>$(function() { $('pre').addClass('prettyprint'); $('.loading').removeClass('loading');
})</script>
  </body>
</html>
