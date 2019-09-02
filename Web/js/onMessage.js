var card_people = 0; // 东家先打
var east_card = [];
var north_card = [];
var south_card = [];
var west_card = [];
var card = new Object();

ws.onmessage = function (evt) {
  var data = evt.data;
  data = JSON.parse(data);
  switch (data.type){
      case 'licensing_brand':
          var list = data.list;
          listBrand(list.east,'east');
          listBrand(list.north,'north');
          listBrand(list.south,'south');
          listBrand(list.west,'west');

          // 赖子
          var image = "image/majiang/"+list.lai+".png";

          $("#laizi").append("<div class='majiang'  style=\"background-image:url("+image+")\"></div>");

          // start play card
          start_play_car();

          break;
      case 'play_brand':
            var number = 0;
            $("#"+data.people+' div').each(function (i,e) {
                var data_key = $(this).attr("data-key");
                if(data_key == data.data && number ==0){
                    $(this).remove();
                    number++;
                    var image = "image/majiang/"+data.data+".png";
                    $("#play_bran").append("<div class='majiang'  style=\"background-image:url("+image+")\"></div>");
                }
            });
            canPeng(data.data);
          break;
      case 'peng':
          var number = 0;
          $("#"+data.people+' div').each(function (i,e) {
              var data_key = $(this).attr("data-key");
              if(data_key == data.data && number < 2){
                  $(this).remove();
                  number++;
                  var image = "image/majiang/"+data.data+".png";
                  $("#play_bran").append("<div class='majiang'  style=\"background-image:url("+image+")\"></div>");
              }
          });
      default:
          break;
  }
};

function canPeng(brand) {
    var data = new Object();
    data.type = 'three_families';
    data.people = card.people;
    data.brand = brand;
    sendMsg(data);
}

function start_play_car() {
    card.type = 'play_car';
    card.people = card_people;
    switch (card_people) {
        case 0:
            card.data = east_card;
            sendMsg(card);
            break;
        case 1:
            card.data = north_card;
            sendMsg(card);
            break;
        case 2:
            card.data = south_card;
            sendMsg(card);
            break;
        case 3:
            card.data = west_card;
            sendMsg(card);
            break;
        default:
            break;
    }
}

/**
 * 开牌
 * @param data
 * @param name
 */
function listBrand(data, name) {
    $.each(data,function (i,e) {
        var image = "image/majiang/"+e+".png";
        $("#"+name).append("<div class='majiang' data-key="+e+" style=\"background-image:url("+image+")\"></div>");

        switch (name){
            case 'east':
                east_card.push(e);
                break;
            case 'north':
                north_card.push(e);
                break;
            case 'west':
                south_card.push(e);
                break;
            case 'south':
                west_card.push(e);
                break;
            default:
                break;
        }
    });
}