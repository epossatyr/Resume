var app = require('express')();
var http = require('http').createServer(app);
var io = require('socket.io')(http);    
var express = require('express');
process.env.PWD = process.cwd()
app.use(express.static(process.env.PWD + '/public'));


var playerTurn = 0;
var playerArray = new Array();
var Deck;
var isSlappable = false;
var usernames = {};
var inChallenge = false;
app.get('/', function(req, res){
    res.sendFile(__dirname + '/public/index.html');
});

// app.use(express.static(__dirname));

io.on('connection', function(socket){ 
    io.emit('connected', socket.id);
    socket.on('disconnect', function(){
        console.log('A user (' + socket.id + ') disconnected');
        delete usernames[socket.id];
        console.log(usernames);
    });
});

io.on('connection', function(socket){
    socket.on('deal', function(playerSock){
        playerDeal(playerSock);
    });
    socket.on('slap', function(hand, slapperSock){
        slap(slapperSock, hand);
    });
    socket.on('newUser', function(name){
        console.log('hit new user');
        usernames[socket.id] = name;
        if(Object.keys(usernames).length > 1){
            gameSetup(socket.id);
        }
    });
});

http.listen(3000, function(){
    console.log('listening on *:3000');
});

function gameSetup(socketId){
    // console.log(usernames);
    let cards = new Array();
    for(let i = 1; i <= 52; i++){
        cards.push(i);
    }
    Deck = new Hand(cards);
    Deck.shuffleHand();
    // console.log(Deck);
    var len = Deck.getHand().length / Object.keys(usernames).length;
    for(let [key, value] of Object.entries(usernames)){
        var cardArray = new Array();
        for(let i = 0; i < len; i++){
            cardArray.push(Deck.getHand().pop());
        }
        playerArray.push(new Hand(cardArray, value, key));
    }
    io.emit('start game', usernames, Deck.getHand());
    console.log(playerArray[0]);
    console.log(playerArray[1]);
    //Let player 1 take their turn
    io.emit('change turn', playerArray[0].name);
}

function playerDeal(playerSock){
    // hand = playerArray.find(a => a.socket == playerSock);
    // console.log(playerArray[playerTurn]);
    // console.log(hand);
    // setTimeout(function(){
        if(playerArray[playerTurn].socket == playerSock){
            //continue to play, deal the card to the main deck
            play(playerArray[playerTurn].dealCard());
            console.log(Deck);
            console.log(playerArray[playerTurn]);
            io.emit('display main', Deck.getHand());
            changeTurn();
        }
        handCheck();
    // }, 1000);
}
function changeTurn(){
    if(playerTurn == 1)
        playerTurn = 0;
    else playerTurn = 1;
    io.emit('change turn', playerArray[playerTurn].name);
}


function play (card) 
{
    io.emit('show card', card);
	isSlappable = false;
    Deck.addTopCard(card);
    setTimeout(function(){
    io.emit('display main', Deck.getHand());
	if (card==(card+13) || card==(card+26) || card==(card+39) || card==(card-13) || card==(card-26) || card==(card-39))
	{
		isSlappable = true;
	}
	
	if (card == 1 || card==14 || card==27 || card==40)
	{
        changeTurn();
        inChallenge = true;
        challenge(4);
	}
	
	if (card==13 || card==26 || card==39 || card==52)
	{
		changeTurn();
        inChallenge = true;
        challenge(3);
	}
	
	if (card==12 || card==25 || card==38 || card==51)
	{
		changeTurn();
        inChallenge = true;
        challenge(2);
	}
	
	if (card==11 || card==24 || card==37 || card==50)
	{
        changeTurn();
        inChallenge = true;
        setTimeout(function(){challenge(1);}, 3000);
	}
	
	if (challenge == 0)
	{
		changeTurn();
    }
    
    io.emit('display main', Deck.getHand());
}, 1000);

setTimeout(function(){
    io.emit('display main', Deck.getHand());
}, 1000);
}
function challenge (chance) 
{
    console.log(playerArray[0]);
    console.log(playerArray[1]);
    io.emit('challenge', playerArray[playerTurn].name, chance);
	while (chance > 0)
	{
        play(playerArray[playerTurn].dealCard());
        if(!inChallenge) break;
		chance -= 1;
    }
    if(inChallenge){
        changeTurn();        
            io.emit('display main', Deck.getHand());
            let len = Deck.getHand().length;
            for(var i = 0; i < len; i++){
                playerArray[playerTurn].addCardToBottom(Deck.dealCard());
                setTimeout(function(){
                io.emit('display main', Deck.getHand());
                },1000);
            }
            inChallenge = false;
            io.emit('stack winner', playerArray[playerTurn].name);
            io.emit('display main', Deck.getHand());
    }
    
    setTimeout(function(){
        io.emit('display main', Deck.getHand());
    }, 1000);
    // Deck.getHand().forEach(c => {playerArray[playerTurn].addTopCard(c);
    //     var crd = Deck.dealCard();
    // });
}
function slap (slapperSock, hand)
{
	if (isSlappable == true)
	{
        Deck.getHand().forEach(c => 
            playerArray.filter(hnd => {return hnd.socket == slapperSock}).addCardToBottom(c));
        isSlappable = false;
	}
	
	else
	{
        for(let i = 0; i < 2; i++){
            Deck.addTopCard(playerArray.filter(hnd => {return hnd.socket == slapperSock;}).dealCard());
        }
	}
}

function handCheck()
{
	if(playerArray[0].getHand() < 1)
	{
		changeTurn();
        console.log('(' + playerArray[0].name + ') has won!');
        io.emit('winner', playerArray[0].name);
    }
    if(playerArray[1].getHand() < 1)
	{
		changeTurn();
        console.log('(' + playerArray[1].name + ') has won!');
        io.emit('winner', playerArray[1].name);
	}
}

class Hand {
    
    constructor(cards = [52], name = "main-deck", socket = null){
        this.cards = cards;
        this.name = name;
        this.socket = socket;
    }
    getName()
    {
        return this.name;
    }
    setName(name)
    {
        if(name){
            this.name = name;
        }
    }
    getHand()
    {
        return this.cards;
    }
    getSocket()
    {
        return this.socket;
    }
    setSocket(socket)
    {
        if(socket){
            this.socket = socket;
        }
    }
    addTopCard(cardVal)
    {
        this.cards.push(cardVal);
    }
    addCardToBottom(cardVal){
        this.cards.unshift(cardVal);
    }
    dealCard()
    {
        return this.cards.pop();
    }
    shuffleHand()
    {
        let len = this.cards.length;
        while(len > 0){
            let i = Math.floor(Math.random() * len) + 1;
            len--;
            let curr = this.cards[len];
            this.cards[len] = this.cards[i];
            this.cards[i] = curr;
        }
        return this.cards
    }
}