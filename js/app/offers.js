

function scanOffers() {
    return jQuery.Deferred(function(dfd) {
        //                    console.log("fetchmemberss start");
        jQuery("#loginPanel").fadeOut();
        jQuery("#loading").fadeIn();

        viewModel.offers([])


        FB.api({
            method: 'fql.multiquery',
            queries: {
                likes: 'SELECT page_id FROM page_fan WHERE uid = me()',
                offers: 'SELECT id, claim_limit, owner_id, title, image_url, coupon_type, terms, claim_limit, created_time, expiration_time, redemption_code, redemption_link FROM offer WHERE owner_id IN (SELECT page_id FROM #likes)',
                from: 'SELECT page_id,name, username, description, page_url, categories, pic_small, pic_big, pic_square, pic, pic_cover, fan_count  FROM page WHERE page_id IN (SELECT owner_id FROM #offers)'
            }
        }, function(resp) {
            //return an array 3 objects
            // response[0]['fql_result_set']   ->lists | memberslist | friends

            //console.log(resp[2].fql_result_set);
            var offers = resp[1].fql_result_set;
            var pages = resp[2].fql_result_set;
            var likes = resp[0].fql_result_set;
                        
            viewModel.likes(likes);

            // fetching pages
            _totalOffers = [];
            jQuery.each(pages,function(i,v){
                totalOffers = jQuery.grep(offers, function(e) {
                    //console.log("ss",e,v)
                    if(e.owner_id == v.page_id){
//                        _totalOffers.push(e)
//                        v.totalOffers = _totalOffers.length;
                    }
                    return e.owner_id == v.page_id;
                }).length;;
                
                            v.totalOffers = totalOffers;
                viewModel.pages.push(v);
            });
           
            // console.log("totalOffers",totalOffers)
            jQuery.each(offers, function(i, v) {

                var from = jQuery.grep(pages, function(e) {
                    return e.page_id == v.owner_id;
                });
                v.from = from[0];

                var endTime = new Date(v.expiration_time * 1000);

                v.endTime = endTime.toISOString();
                v.endTimeHuman = endTime.toLocaleString();
                
                v.expired = utils.isExpired(v.expiration_time);
                
                

                viewModel.offers.push(v);
            // console.log("Fromm", v.from)

            });



            jQuery("#offersContainer").isotope({
                itemSelector: ".item",
                layoutMode: 'masonry',
                onLayout: function(){viewModel.totalOfferShow(viewModel.offers().length-(this.find(".isotope-hidden").length))}

            });

            jQuery.timeago.settings.allowFuture = true;
            jQuery("time.timeago").timeago();

            jQuery("#loading").fadeOut();

        });
    }).promise();
}
;
















       