<!DOCTYPE html>
<html lang="en" xmlns:v-bind="http://www.w3.org/1999/xhtml" xmlns:v-on="http://www.w3.org/1999/xhtml">
<head>
    <title>TicketSoko - Click & Pay</title>
    <link rel="shortcut icon" href="https://pbs.twimg.com/profile_images/915823441172992000/1w-QFqS1.jpg" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!--   <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
       <link rel="stylesheet" type="text/css" href="assets/css/styles.css">
       <link rel="stylesheet" type="text/css" href="assets/css/demo.css">-->

    <style>
        body {
            position: inherit;
        }
        .affix {
            border: 0;
            background-color: white;
            top:0;
            width: inherit;
            z-index: 0001 !important;
        }
        .navbar {
            width: inherit;
            margin-bottom: 0px;
            border: 0;
            border-bottom: 0;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-top-right-radius: 0;
            border-top-left-radius: 0;
        }

        .affix ~ .container {
            width: inherit;
            position: inherit;
            top: 50px;
            background-color: white;
            border: 0;
        }
        .outer{
            width:inherit;
            height:inherit;
            white-space: nowrap;
            position: relative;
            overflow-x:scroll;
            overflow-y:hidden;
        }
        /*  //eee*/

        .outer div{
            padding: inherit;
            width: 220px;
            background-color: inherit;
            float: none;
            height: inherit;
            margin: 0 0.25%;
            display: inline-block;
            zoom: 1;
        }
        .fa {
            padding: inherit;
            font-size: 18px;
            width: inherit;
            text-align: center;
            text-decoration: none;
            margin: 5px 2px;
            border-radius: 40%;
        }

        .fa:hover {
            opacity: 0.7;
        }

        .fa-facebook {
            background: #3B5998;
            color: white;
        }

        .fa-twitter {
            background: #55ACEE;
            color: white;
        }

        .fa-google {
            background: #dd4b39;
            color: white;
        }

        .fa-linkedin {
            background: #007bb5;
            color: white;
        }

        .fa-youtube {
            background: #bb0000;
            color: white;
        }

        .fa-instagram {
            background: #125688;
            color: white;
        }

        .fa-snapchat-ghost {
            background: #fffc00;
            color: white;
            text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;
        }

        .fa-skype {
            background: #00aff0;
            color: white;
        }
        #searchInput[type=text] {
            width: inherit;
            box-sizing: border-box;
            border: 1px solid  #FF6347;
            border-radius: 4px;
            font-size: 15px;
            background-color: white;
            background-image: url('http://pluspng.com/img-png/search-button-png-search-icon-png-image-9966-600.png');
            background-size: 18px;
            background-position: 10px 10px;
            background-repeat: no-repeat;
            padding: 8px 16px 8px 36px;
            -webkit-transition: width 0.4s ease-in-out;
            transition: width 0.4s ease-in-out;
        }

        #searchInput[type=text]:focus {
            width: inherit;
            border: 1px solid  #FF6347;
        }


    </style>

</head>
<body data-spy="scroll" data-target=".navbar" >


<div class="container" id="app">
    <nav class="navbar " data-spy="affix" data-offset-top="0" style="background-color: white">
        <div class="container" >
            <div  class="col-xs-8" align="left">
                <br>
                <input id="searchInput" type="text" name="search" placeholder="Search.."  v-model="searchEvents">
                <!--  <input align="center"   type="text"  style="width: inherit ; text-align: center" class="form-control" placeholder="Search Event" v-model="searchEvents">-->

            </div>
            <div  align="right" class="col-xs-4">
                <img  align="right" style="height: 60px" src="images/logo.jpg">
            </div>
        </div>
        <br>
    </nav>

    <div class="container" >
        <!--START OF CAROUSEL-->
        <div id="myCarousel" class="carousel slide" data-ride="carousel" align="center">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#myCarousel" data-slide-to="1"></li>
                <li data-target="#myCarousel" data-slide-to="2"></li>
                <li data-target="#myCarousel" data-slide-to="3"></li>

            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner">
                <div class="item active">
                    <img src="https://ticketsoko.com/images/main.jpg" style="width: inherit" style="height: inherit">
                </div>


                <div class="item">
                    <img src="https://ticketsoko.com/images/www.jpg"  style="width: inherit" style="height: inherit">
                </div>
                <div class="item">-->
                   <img src="https://ticketsoko.com/images/3.jpg"  style="width: inherit" style="height: inherit">
                 </div>


            </div>

            <!-- Left and right controls -->
            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <!--END OF CAROUSEL-->
        <h3 align="center" style="font-family: Algerian">EVENTS</h3>
        <div class="row">
            <div class="col-md-4" v-for="event in events">
                <div class="thumbnail">
                    <img :src="event.Events.event_image" >
                    <div class="caption">
                        <p> <span class="glyphicon glyphicon-calendar"></span> {{event.Events.event_date}}</p>
                        <p><span class="glyphicon glyphicon-map-marker"></span> {{event.Events.event_venue}}</p>
                        <p><span  class="glyphicon glyphicon-tag"></span> {{event.Events.event_company}}</p>
                        <button type="button" class="btn btn-warning" style="background-color: #FF6347" data-toggle="modal" v-on:click="setOptions(event.Options,event.Events.event_name,event.Merchandise,event.Events.id,event.Events.event_company,event.Events.event_image,event.Events.transaction_desc)" data-target="#checkoutModal">Buy now</button>
                        
                    </div>
                </div>
            </div>
        </div>

        <h3 align="center" style="font-family: Algerian"> PAST EVENTS</h3>
        <div  class="outer" >
            <div v-for="pastEvent in pastEvents" >
                <div class="thumbnail">
                    <img :src="pastEvent.Events.event_image">
                    <div class="caption">
                        <p> <span class="glyphicon glyphicon-calendar"></span> {{pastEvent.Events.event_date}}</p>
                        <p><span class="glyphicon glyphicon-map-marker"></span> {{pastEvent.Events.event_venue}}</p>
                        <p><span  class="glyphicon glyphicon-tag"></span> {{pastEvent.Events.event_company}}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="checkoutModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #FF6347 ;color: white">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" align="center" style="font-family: Serif bold ">TICKETSOKO CHECKOUT</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-3" style="background-color:white;"> <label> Events</label></div>
                            <div class="col-xs-4" style="background-color:white;"><label> Ticket Options</label></div>
                            <div  align="center" class="col-xs-2" style="background-color:white;"> <label> #</label></div>
                            <div class="col-xs-3" style="background-color:white;"><label> Value(KES)</label></div>
                        </div>
                        </br>

                        <div class="row" v-for="Option in Options">
                            <div class="col-xs-3" style="background-color:white;;"> <h5>{{eventName}}</h5></div>
                            <div class="col-xs-4" style="background-color:white;">

                                <select class="form-control" v-model="selectedOptionID">
                                    <option v-for="OptionChoice in getChoices(Option.OptionChoice)" :value="OptionChoice.id" :key="OptionChoice.id">{{ optionName(OptionChoice.name,Option.Choice.seasonal)}} {{ OptionChoice.price}} {{setSelectedMeRegular(Option.Choice.seasonal)}}</option>
                                </select>

                            </div>
                            <div class="col-xs-3" style="background-color:white;"> <input type="number" class="form-control" min="0"  v-model="valueRegular"/> </div>
                            <div class="col-xs-2" style="background-color:white;"><h5>{{getTotalMeRegular(Option.Choice.seasonal)}}</h5></div>
                        </div>
                        <!--{{selectedOptionID}} </br>{{selectedPrice}}-->
                        </br>

                        <!--<input type="checkbox" checked="checked" v-model="checkboxValue" v-if="Merchandises != null ">  <label v-if="Merchandises != null ">Children Tickets</label><br>-->
                        <!--<div class="row" v-for="Merchandise in Merchandises">-->
                        <!--    <div class="col-xs-7" style="background-color:white;"> <h5>{{Merchandise.product}} -KES {{Merchandise.price}} </h5></div>-->
                        <!--    <div class="col-xs-3" style="background-color:white;"> <input type="number" class="form-control" :value="0" min="0" v-model="Merchandise.id"> </div>-->
                        <!--    <div class="col-xs-2" style="background-color:white;"><h5>{{getTotalMerchandise(Merchandise.item_no,Merchandise.id,Merchandise.price,Merchandise.product)}}</h5></div>-->
                        <!--</div>-->
                        <br>
                        <div align="center">
                            TOTAL <button align="center" type="button" class="btn btn-warning" style="background-color: #FF6347" >KES {{getSum()}}</button>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xs-6" style="background-color:white">
                                <label>Mobile Checkout</label>
                                <h5>Enter Mobile Number</h5>
                                <input type="tel" class="form-control"  placeholder="07xxxxxxxx" v-model="phone"/><br>
                                <button align="center" type="button" class="btn btn-warning" style="background-color: #FF6347" v-on:click="checkout()">Pay</button>
                            </div>
                            <div class="col-xs-6" style="background-color:white;">
                                <label>Card Checkout</label>
                                <div class="creditCardForm">
                                    <div class="payment">
                                        <form>
                                            <div class="form-group owner">
                                                <h5>Enter Name</h5>
                                                <input type="text" class="form-control" id="owner">
                                            </div>
                                            <div class="form-group" id="card-number-field">
                                                <h5>Card Number</h5>
                                                <input type="text" class="form-control" id="cardNumber">
                                            </div>
                                            <div class="form-group CVV">
                                                <div class="row" >
                                                    <div class="col-xs-8">
                                                        <h5>Expiry Date</h5>
                                                        <input type="text" class="form-control" id="expiry">
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <h5>CVV</h5>
                                                        <input type="text" class="form-control" id="cvv">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group" id="email_address">
                                                <h5>Email Address</h5>
                                                <input type="text" class="form-control" id="email">
                                            </div>
                                            <div class="form-group" id="credit_cards">
                                                <img src="assets/images/visa.jpg" id="visa">
                                                <img src="assets/images/mastercard.jpg" id="mastercard">
                                                <img src="assets/images/amex.jpg" id="amex">
                                            </div>
                                            <div class="form-group" id="pay-now">
                                                <button type="submit" class="btn btn-warning" style="background-color: #FF6347"  id="confirm-purchase">Pay</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"  style="background-color: #FF6347 ;color: white">
                        <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<footer class="text-center">

    <div align="center" style="margin-top: 10px">
        <a href="https://www.facebook.com/TicketSoko-118258788840525/?ref=br_rs" class="fa fa-facebook" style="padding: 10px" target="_blank"></a>
        <a href="https://twitter.com/search?q=ticketsoko&src=typd" class="fa fa-twitter"  target="_blank" style="padding: 10px"></a>
        <a href="#" class="fa fa-google"  style="padding: 10px"  target="_blank"></a>
        <a href="#" class="fa fa-linkedin"  style="padding: 10px"  target="_blank"> </a>
        <a href="#" class="fa fa-youtube"  style="padding: 10px"  target="_blank"></a>
        <a href="https://www.instagram.com/ticketsoko/?hl=en" target="_blank" class="fa fa-instagram"  style="padding: 10px"></a>
        <a href="#" class="fa fa-skype"  style="padding: 10px"  target="_blank"></a>
    </div>
    <a href="#" class="fa fa-phone"  style="padding: 10px"  target="_blank"></a>
    <span>For assistance please call : 0729703770 </span>

    <div class="container">
        <a href="http://nouveta.tech" target="_blank">
            <table align="right">
                <tr>
                    <td align="center">
                        <p style="size: 20px"><b>Powered by</b> </p>
                    <td>
                        <br>
                        <div style="padding-left: 20px">
                            <img  align="left" style="height: 50px ;margin-top: 5px" src="images/nouveta.jpg" >
                        </div>

                    </td>
                </tr>
            </table>
        </a>
    </div>
    <br>
    <br>

</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="assets/js/jquery.payform.min.js" charset="utf-8"></script>
<script src="assets/js/script.js"></script>

</body>

<script src="https://unpkg.com/vue@2.0.3/dist/vue.js"></script>
<script src="https://unpkg.com/axios@0.12.0/dist/axios.min.js"></script>
<script src="https://unpkg.com/lodash@4.13.1/lodash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js"></script>


<script>
    var app = new Vue({
        el: '#app',
        data: {
            events: [],
            pastEvents: [],
            Options:[],
            eventName:'',
            event_company:'',
            transaction_desc:'',
            checkboxValue:false,

            Merchandises:[],
            totalItemNo1:0,
            totalItemNo2:0,
            totalItemNo3:0,
            totalItemNo4:0,
            totalItemNo5:0,
            valueItemNo1:0,
            valueItemNo2:0,
            valueItemNo3:0,
            valueItemNo4:0,
            valueItemNo5:0,
            descriptionItemNo1:'',
            descriptionItemNo2:'',
            descriptionItemNo3:'',
            descriptionItemNo4:'',
            descriptionItemNo5:'',

            totalRegular:0,
            totalSeasonal:0,
            valueRegular:0,
            valueSeasonal:0,

            OptionChoices:[],
            OptionChoiceSelectedRegular:'',
            OptionChoiceSelectedRegularName:'',
            OptionChoiceSelectedSeasonal:'',

            event_id:'',
            totalSum:0,
            boolean:true,
            phone:'' ,
            selected:'',
            searchEvents:'',
            image:'',
            selectedOptionID:'',
            selectedPrice:0,
            day:''
        },
        watch: {
            searchEvents: function() {
                this.getEvents();
            }
        },
        methods: {
            setOptions: function(options,eventName,Merchandise,id,event_company,image,transaction_desc){
                this.Options=options;
                this.eventName = eventName;
                this.Merchandises =Merchandise;
                this.event_id = id;
                this.event_company =event_company;
                this.image =image;
                this.transaction_desc =transaction_desc;
            },
            optionName: function(name,seasonal){
                if(seasonal==='0'){
                    this.OptionChoiceSelectedRegular = name;
                    return this.OptionChoiceSelectedRegular
                }else {
                    this.OptionChoiceSelectedSeasonal = "Seasonal "+name;
                    return this.OptionChoiceSelectedSeasonal;
                }
            },
            setSelectedMeRegular: function(seasonal){
                if(seasonal==='0'){
                    this.totalRegular = this.selectedPrice * this.valueRegular;
                }else {
                    this.totalSeasonal =this.selectedPrice *  this.valueSeasonal;

                }
            },getTotalMeRegular: function(seasonal){
                if(seasonal==='0'){
                    this.getOptions();
                    return this.totalRegular;
                }else {
                    this.valueSeasonal = value;
                    return this.totalSeasonal;
                }
            },
            getTotalMerchandise: function(item_no,value,price,product){
                if(!this.checkboxValue){
                    this.totalItemNo1=0;  this.totalItemNo2=0;  this.totalItemNo3=0;  this.totalItemNo4=0;  this.totalItemNo5=0;
                    return
                }
                if(item_no === '1'){
                    this.descriptionItemNo1 = product + " KES "+price;
                    this.valueItemNo1 = value;
                    return this.totalItemNo1 = value * price;
                }if(item_no === '2'){
                    this.descriptionItemNo2 = product + " KES "+price;
                    this.valueItemNo2 = value;
                    return this.totalItemNo2 = value * price;
                }if(item_no === '3'){
                    this.descriptionItemNo3 = product + " KES "+price;
                    this.valueItemNo3 = value;
                    return this.totalItemNo3 = value * price;
                }if(item_no === '4'){
                    this.descriptionItemNo4 = product + " KES "+price;
                    this.valueItemNo4 = value;
                    return this.totalItemNo4 = value * price;
                }if(item_no === '5'){
                    this.descriptionItemNo5 = product + " KES "+price;
                    this.valueItemNo5 = value;
                    return this.totalItemNo5 = value * price;
                }
            },
            getSum: function(){
                if(!this.checkboxValue){
                    this.totalItemNo1=0;  this.totalItemNo2=0;  this.totalItemNo3=0;  this.totalItemNo4=0;  this.totalItemNo5=0;
                }

                this.totalSum = parseInt(this.totalSeasonal) + parseInt(this.totalRegular)
                    + parseInt(this.totalItemNo1) + parseInt(this.totalItemNo2)+ parseInt(this.totalItemNo3)
                    + parseInt(this.totalItemNo4) + parseInt(this.totalItemNo5);

                return this.totalSum;
            } ,
            getChoices: function(choice){
                return this.OptionChoices = choice;
            },
            checkout: function(){
                if(this.totalSum===0){
                    alert("You can't checkout KES 0")
                    return
                }
                if(this.phone===''){
                    alert("Enter Mobile number")
                    return
                }
                var formData = new FormData();
                formData.append('function', 'checkOut');
                formData.append('OptionChoiceSelectedRegularName', this.OptionChoiceSelectedRegularName);//Selected[Regular, Vip, Vvip]
                formData.append('valueRegular', this.valueRegular);//NO of Tickets
                formData.append('totalRegular', this.totalRegular);
                formData.append('day', this.day);

                formData.append('OptionChoiceSelectedSeasonal', this.OptionChoiceSelectedSeasonal);//Selected seasonal[Regular, Vip, Vvip]
                formData.append('valueSeasonal', this.valueSeasonal);//NO of Tickets
                formData.append('totalSeasonal', this.totalSeasonal);

                formData.append('valueItemNo1', this.valueItemNo1); //4
                formData.append('valueItemNo2', this.valueItemNo2);
                formData.append('valueItemNo3', this.valueItemNo3);
                formData.append('valueItemNo4', this.valueItemNo4);
                formData.append('valueItemNo5', this.valueItemNo5);
                formData.append('totalItemNo1', this.totalItemNo1);//2000
                formData.append('totalItemNo2', this.totalItemNo2);
                formData.append('totalItemNo3', this.totalItemNo3);
                formData.append('totalItemNo4', this.totalItemNo4);
                formData.append('totalItemNo5', this.totalItemNo5);
                formData.append('descriptionItemNo1', this.descriptionItemNo1);// 3 Quine Hoodie KES 4500
                formData.append('descriptionItemNo2', this.descriptionItemNo2);
                formData.append('descriptionItemNo3', this.descriptionItemNo3);
                formData.append('descriptionItemNo4', this.descriptionItemNo4);
                formData.append('descriptionItemNo5', this.descriptionItemNo5);

                formData.append('totalSum', this.totalSum);//500
                formData.append('event_id', this.event_id);
                formData.append('event_company', this.event_company);
                formData.append('phone_number', this.phone);
                formData.append('event_image', this.image);
                formData.append('transaction_desc', this.transaction_desc);
                axios.post('https://ticketsold.ticketsoko.com/api/index.php',formData)
                    .then(function (response) {
                        console.log(response.data)
                        alert(response.data.message)
                    })
                    .catch(function (error) {

                    })
            },
            getEvents: _.debounce(function() {
                var app = this
                var formData = new FormData();
                formData.append('function', 'getEvents');
                formData.append('keyword', this.searchEvents);
                axios.post('https://ticketsold.ticketsoko.com/api/index.php',formData)
                    .then(function (response) {
                        app.events = response.data.data
                    })
                    .catch(function (error) {

                    })
            }, 500),
            getOptions: _.debounce(function() {
                var app = this
                var formData = new FormData();
                formData.append('function', 'getOptions');
                formData.append('id', this.selectedOptionID);
                axios.post('https://ticketsold.ticketsoko.com/api/index.php',formData)
                    .then(function (response) {
                        this.selectedPrice = parseInt(response.data.data.price)
                        this.OptionChoiceSelectedRegularName = response.data.data.name+" KES "+response.data.data.price
                        this.day = response.data.data.day;
                    }.bind(this))
                    .catch(function (error) {

                    })
            }, 500)
            ,
            getPastEvents: _.debounce(function() {
                var app = this
                var formData = new FormData();
                formData.append('function', 'getPastEvent');
                axios.post('https://ticketsold.ticketsoko.com/api/index.php',formData)
                    .then(function (response) {
                        app.pastEvents = response.data.data
                    })
                    .catch(function (error) {

                    })
            }, 500)
        },
        beforeMount: function(){
            this.getEvents();
            this.getPastEvents();
        }

    })
</script>

</html>