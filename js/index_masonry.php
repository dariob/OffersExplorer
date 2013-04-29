<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>


        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="css/bootstrap.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <style type="text/css">
            /*            body {
                            padding-top: 60px;
                            padding-bottom: 40px;
                        }
                        .sidebar-nav {
                            padding: 9px 0;
                        }*/
        </style>
        <link href="css/bootstrap-responsive.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Junge' rel='stylesheet' type='text/css'>
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!--[if gte IE 9]>
          <style type="text/css">
            .gradient {
               filter: none;
            }
          </style>
        <![endif]-->

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script src="js/knockout-2.1.0.js"></script>
        <script src="js/knockout.mapping-latest.debug.js"></script>
        <script src="js/jquery.masonry.min.js"></script>
        <script src="js/jquery.timeago.js"></script>
    </head>
    <body>
        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '144289115668589', // App ID
                    channelUrl : '//localhost/exploreoffers/channel.html', // Channel File
                    status     : true, // check login status
                    cookie     : true, // enable cookies to allow the server to access the session
                    xfbml      : true  // parse XFBML
                });

                // Additional initialization code here
                FB.getLoginStatus(function(response) {
                    // this will be called when the roundtrip to Facebook has completed
                    FB.api('me',function(r){
                        viewModel.user(r);  
                        console.log("get login statur",r);
                        if(r.name){
                            scanOffers();
                            jQuery("#loginPanel").fadeOut();
                        }
                        
                    });
                }, true);
    
            };

            // Load the SDK Asynchronously
            (function(d){
                var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement('script'); js.id = id; js.async = true;
                js.src = "//connect.facebook.net/en_US/all.js";
                ref.parentNode.insertBefore(js, ref);
            }(document));
        </script>

        <script>
         
      
            function scanOffers(){
                return jQuery.Deferred(function(dfd){
                    //                    console.log("fetchmemberss start");
                    jQuery("#loginPanel").fadeOut();
                    jQuery("#loading").fadeIn();
                     
                    viewModel.offers([])
    
        
                    FB.api({
                        method: 'fql.multiquery', 
                        queries: {
                            likes: 'SELECT page_id FROM page_fan WHERE uid = me()',
                            offers: 'SELECT id, claim_limit, owner_id, title, image_url, coupon_type, terms, claim_limit, created_time, expiration_time, redemption_code, redemption_link FROM offer WHERE owner_id IN (SELECT page_id FROM #likes)' ,
                            from: 'SELECT page_id,name, username, description, page_url, categories, pic_small, pic_big, pic_square, pic, pic_cover, fan_count  FROM page WHERE page_id IN (SELECT owner_id FROM #offers)'
                        }
                    }, function(resp){
                        //return an array 3 objects
                        // response[0]['fql_result_set']   ->lists | memberslist | friends
        
                        console.log(resp[2].fql_result_set);
                        var offers = resp[1].fql_result_set;
                        var pages  = resp[2].fql_result_set;
                         
                        jQuery.each(offers,function(i,v){
                            
                            var from = jQuery.grep(pages,function(e){return e.page_id == v.owner_id});
                            v.from = from[0];
                            
                            var endTime = new Date(v.expiration_time *1000);
                           
                            v.endTime = endTime.toISOString();
                            v.endTimeHuman = endTime.toLocaleString();
                            
                            viewModel.offers.push(v);
                            console.log("Fromm",v.from)
                            
                        });
                        
                        
                        
                        jQuery("#pinLayout").masonry({
                            itemSelector: ".item"
                           
                        });
                        
                        jQuery.timeago.settings.allowFuture = true;
                        jQuery("time.timeago").timeago();
                        
                        jQuery("#loading").fadeOut();
        
                    });
                }).promise();
            };
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
        </script>

        <div id="header">
            <h1>Explore Offers</h1>

        </div>


        <div id="loginPanel">
            <fb:login-button size="small" scope="user_likes" onlogin="scanOffers()">
                Connect to discover offers from Facebook Pages that you like
            </fb:login-button>


        </div>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span12">

                    <div id="loading">
                        <img src="img/loading.gif" />
                    </div>

                    <div id="pinLayout" data-bind="foreach: offers">
                        <div class="item" data-bind="attr:{id:id}">
                            <div class="topItem">
                                <div class="from">                               
                                    <a target="_blank" data-bind="attr:{href:from.page_url, title:from.name}">                                   
                                        <img data-bind="attr: {src:from.pic_square, alt:from.name, title:from.name}" />
                                        <span data-bind="text: from.name" class="owner_name"></span>
                                    </a>

                                </div>
                                <h4 data-bind="text: title"></h4>
                            </div>
                            <div class="bodyItem">
                                <img data-bind="attr: {src:image_url, title:title, alt:title}" />
                                <p class="terms" data-bind="text: terms"></p>
                            </div>
                            <div class="footerItem">
                                <hr>
                                <a target="_blank" data-bind="attr:{href:redemption_link}">Redemption link</a>                            
                                <p data-bind="if: redemption_code">
                                    Coupon code: <span class="couponCode" data-bind="text:redemption_code"></span>
                                </p>


                                <hr>

                                <div class="details">
                                    <div data-bind="if: claim_limit > 0">
                                        <span>Coupons available: </span>
                                        <span data-bind="text:claim_limit"></span>
                                    </div>
                                    expiry:
                                    <time class="timeago" data-bind="text: endTimeHuman, attr:{datetime:endTime}"></time>
                                    <!--<span data-bind="text: coupon_type"></span>-->
                                </div>


                            </div>
                        </div>
                    </div>


                   
                </div>
            </div>
        </div>







 <div class="footer">
                        <small>handmade by <a href="http://fb.com/dario.barila" target="_blank">Dario Barilà</a></small>
                    </div>











        <script>
            viewModel = {
            
                user: ko.observable({}),
                offers: ko.observableArray([])
            
            };
        
            ko.applyBindings(viewModel);
        </script>


        <script>
            jQuery(function(){
      
            });
        </script>
    </body>
</html>
