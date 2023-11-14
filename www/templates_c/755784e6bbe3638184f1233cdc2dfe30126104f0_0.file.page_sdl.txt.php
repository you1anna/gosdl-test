<?php
/* Smarty version 3.1.31, created on 2023-11-13 14:56:49
  from "/Users/robin.miklinski/rg/gosdl/www/templates/page_sdl.txt" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_65523931c60b22_41513745',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '755784e6bbe3638184f1233cdc2dfe30126104f0' => 
    array (
      0 => '/Users/robin.miklinski/rg/gosdl/www/templates/page_sdl.txt',
      1 => 1699455219,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_65523931c60b22_41513745 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SDL</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">

  <style>
  
  .indentedCheckbox {
    margin-left: 25px;
  }

  .glyphicon.spinning {
      animation: spin 1s infinite linear;
      -webkit-animation: spin2 1s infinite linear;
  }

  @keyframes spin {
      from { transform: scale(1) rotate(0deg); }
      to { transform: scale(1) rotate(360deg); }
  }

  @-webkit-keyframes spin2 {
      from { -webkit-transform: rotate(0deg); }
      to { -webkit-transform: rotate(360deg); }
  }


  /* Space out content a bit */
  body {
    padding-top: 20px;
    padding-bottom: 20px;
  }

  /* Everything but the jumbotron gets side spacing for mobile first views */
  .header,
  .marketing,
  .footer {
    padding-right: 15px;
    padding-left: 15px;
  }

  /* Custom page header */
  .header {
    padding-bottom: 20px;
    border-bottom: 1px solid #e5e5e5;
    background-repeat: no-repeat;
    background-position: 90px -20px;
    background-size: 160px;

  }
  /* Make the masthead heading the same height as the navigation */
  .header h3 {
    margin-top: 0;
    margin-bottom: 0;
    line-height: 40px;
  }

  /* Custom page footer */
  .footer {
    padding-top: 19px;
    color: #777;
    border-top: 1px solid #e5e5e5;
  }

  /* Customize container */
  @media (min-width: 768px) {
    .container {
      max-width: 730px;
    }
  }
  .container-narrow > hr {
    margin: 30px 0;
  }

  /* Main marketing message and sign up button */
  .jumbotron {
    text-align: center;
    border-bottom: 1px solid #e5e5e5;

  }
  .jumbotron .btn {
    padding: 14px 24px;
    font-size: 21px;
  }

  /* Supporting marketing content */
  .marketing {
    margin: 40px 0;
  }
  .marketing p + h4 {
    margin-top: 28px;
  }

  /* Responsive: Portrait tablets and up */
  @media screen and (min-width: 768px) {
    /* Remove the padding we set earlier */
    .header,
    .marketing,
    .footer {
      padding-right: 0;
      padding-left: 0;
    }
    /* Space out the masthead */
    .header {
      margin-bottom: 30px;
    }
    /* Remove the bottom border on the jumbotron for visual effect */
    .jumbotron {
      border-bottom: 0;
    }
  }

  
  </style>


</head>
<body>
  <div class="container">


    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body">
            <div>Please confirm the JIRA EPIC id for your project:</div>
            <b><div id="epic"></div></b>
            <div>A new PRODSEC JIRA ticket that contains SDL checklist will be created and linked to this EPIC after completing this form</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="startSurvey()">Ok</button>
          </div>
        </div>
      </div>
    </div>

    <div class="header clearfix">
      <nav>
        <ul class="nav nav-pills pull-right">
          <!--<li role="presentation" class="active"><a href="#">Home</a></li>-->
          <!-- <li role="presentation"><a href="#">Issues</a></li> -->
          <!-- <li role="presentation"><a href="#">Feedback</a></li> -->
        </ul>
      </nav>
      <h3 class="text-muted">goSDL</h3>
    </div>
    <div class="well" id="initialinstructions">
      <p class="lead">Instructions</p>
      <p>
        <ol>
          <li>At the middle or near the end of completion of a project, have a technical person to fill the SDL form.</li>
          <li>After the initial risk assessment is completed, please complete the Component checklist on the next page. The person filling out this form should check anything that is relevant to the code / feature (language-wise and context-wise) and uncheck anything that <i>they know</i> will always be irrelevant to the project. It's ok to check more things than you need, as there's a way to "uncheck" them later.</li>
          <li>After the form submitted there will be a JIRA ticket or Trello board created with the checklist items.</li>
          <li>The goal of the SDL is to have <i>everything</i> checked off. If there is an issue with one of the items, please feel free to ask the Security team for advice and steps on how to move forward. Ideally, a fully-completed SDL checklist will expedite the security review requirement.</li>
        </ol>
      </p>
    </div>

    <div id="informationGatheringForm" class="jumbotron">
      <p class="lead">First off, we need some info from you about your project</p>

    </div>

    <div class="riskQuestionnaire">

    </div>

    <div class="componentSurvey">

    </div>

    <div class="jiraCompletion">

    </div>

    <footer class="footer">
      <p></p>
    </footer>

  </div> <!-- /container -->

  <!-- JQuery -->

  <!-- JQuery 2.1.4 -->
  <?php echo '<script'; ?>
 src="https://code.jquery.com/jquery-2.1.4.min.js" integrity="sha256-8WqyJLuWKRBVhxXIL1jBDD7SDxU936oZkCnxQbWwJVw=" crossorigin="anonymous"><?php echo '</script'; ?>
>
  <!-- Latest compiled and minified JavaScript -->
  <?php echo '<script'; ?>
 src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"><?php echo '</script'; ?>
>

  <?php echo '<script'; ?>
 type="text/javascript">
    var trello = false;
  <?php echo '</script'; ?>
>
  <?php if (isset($_smarty_tpl->tpl_vars['trello']->value) && $_smarty_tpl->tpl_vars['trello']->value == 'true') {?>
    <?php echo '<script'; ?>
 src="https://trello.com/1/client.js?key=<?php echo $_smarty_tpl->tpl_vars['trello_api_key']->value;?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="js/trello.js?<?php echo $_smarty_tpl->tpl_vars['versions']->value;?>
"><?php echo '</script'; ?>
>

      <?php echo '<script'; ?>
 type="text/javascript">
          var trello = true;
      <?php echo '</script'; ?>
>

  <?php }?>
  <?php echo '<script'; ?>
 src="js/sdl.js?<?php echo $_smarty_tpl->tpl_vars['versions']->value;?>
"><?php echo '</script'; ?>
>
</body>
</html>
<?php }
}
