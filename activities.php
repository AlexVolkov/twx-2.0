<?php
if (!require_once './settings.php')
    die(_('ERROR_CANNOT_LOAD_SETTINGS'));

if (!require_once $config['path'] . 'system/core.actions.php')
    die(_('ERROR_CANNOT_LOAD_CORE_FILE'));

$core = new CoreActions($config);

if (!$core->keyCheck($_COOKIE['key']))
    header("Location:./?nokey");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="./css/css.css" />
        <link REL="SHORTCUT ICON" HREF="./i/favicon.ico" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js" charset="utf-8"></script>
        <title>Activities</title>

        <script type="text/javascript" charset="utf-8">
            function delcookie()
            {
                var tmp_date=new Date()-10;
                document.cookie="key_id=;expires=Thu, 01-Jan-70 00:00:01 GMT;";
                window.location.href="./?loggedout";

            }
            $(function() {
                
                $( "#taskDialog" ).dialog({
                    resizable: false,
                    height:400,
                    width:560,
                    draggable: false,
                    modal: false,
                    autoOpen: false,
                    buttons: {
                        "Add task": function() {

                        },
                        Cancel: function() {
                            $( this ).dialog( "close" );
                        }
                    },
                    close:function() { 
                        $( "#addNew" ).removeClass("onHover");

                    }
                });
                
                
                
                
                
                $( "#addNew" ).click(function() {
                    $( "#addNew" ).addClass("onHover");                    

                    $( "#taskDialog" ).dialog({ position: [30, 40]});
                    $( "#taskDialog" ).dialog("open");
                    return false;
                
                });
                
                
                
            });
        </script>

    </head>

    <body>
        <div id="taskDialog" title="">
            <form method="POST" action="./system/api.php">

                <input type="hidden" name="key_id" value="<?php echo $_COOKIE['key_id']; ?>" />
                <input type="hidden" name="mask" value="<?php echo $core->GenerateMask(); ?>" />
                <input type="hidden" name="what" value="task"/>
                <input type="hidden" name="operation" value="addtask"/>


                <select name="task_type">
                    <option value="single"/>single
                    <option value="feed"/>feed
                    <option value="follow"/>follow
                    <option value="retweet"/>retweet
                </select><br/>

                task name <input type="text" name="task_name" value=""/>
                <textarea name="task_content" ></textarea><br/>
                task_cron_intval <input type="text" name="task_cron_intval" value="0" /><br/>
                <hr/>
                drip <input type="checkbox" name="is_dripped" /> <br/>
                short <input type="text" name="task_shortener" value=""/> <br/>
                threads <input type="text" name="threads" value="10"/> <br/>
                <hr/>
                work_by_sitemap <input type="checkbox" name="work_by_sitemap" /> <br/>
                grab_titles <input type="checkbox" name="grab_titles" /> <br/>
                strip_links <input type="checkbox" name="strip_links" /> <br/>
                use accounts with errora <input type="checkbox" name="use_error_accounts" /> <br/>
                <br/>
                <hr/>
                twi numaccs <input type="text" name="twitter" value=""/>
                idenctica numaccs <input type="text" name="identica" value=""/>
                ping.fm accs <input type="text" name="pingfm" value=""/>

                <input type="submit"/>
            </form> 

        </div>


        <div id ="head">
            <ul class="floatleft">
                <li>
                    <a href="#" class="active">activities</a>
                    <a href="#">acccounts</a>
                </li>
            </ul>
            <ul id="userinfo" class="floatleft aligncenter">
                <li style="margin-left:150px; color:#fff; text-shadow: 0 1px 0 #464545; font-size: 18px;">
                    &nbsp;&nbsp;&nbsp; current tasks list</li>
            </ul>
            <ul class="floatright">
                <li><a href="#">settings</a>
                    <a href="javascript:delcookie();">logout</a></li>
            </ul>
        </div>

        <div id="topbar">

            <div id="topNavigation">

                <div id="addNew" class="topButton firstButton">add new</div>
                <div class="topButton">remove</div>
                <div class="topButton">launch</div>
                <div class="topButton lastButton">stop</div>





            </div>
        </div>



        <!--cleared-->
        <div style="overflow: hidden; position: absolute; visibility: visible; z-index: 0; left: 0px; top: 78px; width: 100%; height: 1054px; background: none repeat scroll 0% 0% rgb(224, 235, 245);">

            <div style="overflow: hidden; position: absolute; visibility: visible; z-index: 0; left: 100px; top: 10px; width: 1094px; height: 604px;">




                <div style="" class="leftTop"></div>
                <div style="overflow: hidden; position: absolute; visibility: visible; z-index: -1000; width: 1094px; height: 1px; background: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAtJREFUCB1jMBYGAAB8AEeDnTStAAAAAElFTkSuQmCC&quot;) repeat scroll 0% 0% transparent; left: 2px; top: 0px;"></div>
                <div style="overflow: hidden; position: absolute; visibility: visible; z-index: -1000; width: 2px; height: 1px; background: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAABCAQAAABeK7cBAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAA1JREFUCB1jMOY0ZgYAAVUAc0bVlxgAAAAASUVORK5CYII=&quot;) repeat scroll 0% 0% transparent; right: 0px; top: 0px;"></div>
                <div style="overflow: hidden; position: absolute; visibility: visible; z-index: -1000; width: 2px; height: 600px; background: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAABCAQAAABeK7cBAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAA1JREFUCB1jMJYxDgUAAeAA2ELKbB4AAAAASUVORK5CYII=&quot;) repeat scroll 0% 0% transparent; left: 0px; top: 1px;"></div>
                <div style="overflow: hidden; position: absolute; visibility: visible; z-index: -1000; width: 1094px; height: 600px; background: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAAAAAA6fptVAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAApJREFUCB1j+A8AAQEBADZfZ4AAAAAASUVORK5CYII=&quot;) repeat scroll 0% 0% transparent; left: 2px; top: 1px;"></div>
                <div style="overflow: hidden; position: absolute; visibility: visible; z-index: -1000; width: 2px; height: 600px; background: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAABCAQAAABeK7cBAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAA1JREFUCB1jMA41lgEAAlIA2CQUfR8AAAAASUVORK5CYII=&quot;) repeat scroll 0% 0% transparent; right: 0px; top: 1px;"></div>
                <div style="overflow: hidden; position: absolute; visibility: visible; z-index: -1000; width: 2px; height: 3px; background: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAADCAQAAAAT4xYKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABdJREFUCB1jMBY2tmQw5jSWYTBmNuYEAA4LAbB4sOr5AAAAAElFTkSuQmCC&quot;) repeat scroll 0% 0% transparent; left: 0px; bottom: 0px;"></div>
                <div style="overflow: hidden; position: absolute; visibility: visible; z-index: -1000; width: 1094px; height: 3px; background: url('./t2.png') repeat scroll 0% 0% transparent; left: 2px; bottom: 0px;"></div>
                <div style="overflow: hidden; position: absolute; visibility: visible; z-index: -1000; width: 2px; height: 3px; background: url('./t1.png') repeat scroll 0% 0% transparent; right: 0px; bottom: 0px;"></div>

            </div>

        </div>



    </body>

</html>
