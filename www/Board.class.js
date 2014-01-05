/** 
	@fileoverveiw Class Board - Wrapper for HTML5 canvas
*/
goog.provide('wa1.components.Board');
goog.require("wa1.controllers.Simulation");

goog.require('goog.ui.Component');

/** 
	Class component
	@constructor
*/
wa1.components.Board = function(element, playerId, enemyId, setWidth, setHeight){
	this.CELL_SIZE = 20;
	element = goog.dom.getElement( element );
	
	if ((setWidth % this.CELL_SIZE) != 0) {
		throw "Width must be divisible by " + this.CELL_SIZE;
	};
	
	if ((setHeight % this.CELL_SIZE) != 0) {
		throw "Height must be divisible by " + this.CELL_SIZE;
	};
	
	if(playerId == undefined) {
		throw "PlayerId must be specified!";
	}
	
	// class 
	this.playerId = playerId;
	this.enemyId = enemyId;
	
	this.width = setWidth;
	this.height = setHeight;
	
	this.moves = new Array();
	
	// model of board
	this.x_count = this.width / this.CELL_SIZE;
	this.y_count = this.height / this.CELL_SIZE;
	this.cells = new Array( this.x_count );
	for(var x = 0; x < this.x_count; x++) {
		this.cells[x] = new Array( this.y_count );
	}
	
	// init component
	var dom = goog.dom.DomHelper();
	this.component = new goog.ui.Component( dom );
	
	// initializes canvas
	this.initCanvas();
	
	element.innerHTML = "";
	this.component.render( element );
	this.renderGrid();
};

// methods ---------------------------------------------
wa1.components.Board.prototype.initCanvas = function() {
	// prepare canvas
	this.canvas = goog.dom.createDom( 'canvas', {
		id: 'boardCanvas',
		width: this.width,
		height: this.height
	});
	
	// set canvas
	this.component.setElementInternal( this.canvas );
	
	// TODO: rewrite listeners
	var that = this;
	this.canvas.onclick = function(event) {
		var x = that.unmap(event.offsetX);
		var y = that.unmap(event.offsetY);
		
		// if there already is life cell of currently playing player, don't do anything
		if((that.cells[x] != undefined) && (that.cells[x][y] == that.playerId)) {
			return;
		}
		
		// board is locked, when canPut is 0
		if(that.locked != undefined) {
			return;
		}
		
		if(that.onPut != undefined) {
			// TODO: cancell by remove
			that.onPut();
		}
		
		that.moves.push( {x:x, y:y, player: that.playerId, move: "put"} );
		that.put( x, y, that.playerId );
	};
	this.canvas.oncontextmenu = function(event) {
		return false;	// TODO: disabled
		
		/*var x = that.unmap(event.offsetX);
		var y = that.unmap(event.offsetY);
		
		if(that.cells[x][y] == that.playerId) {
			that.moves.push( {x:x, y:y, player: that.playerId, move: "remove"} );
			that.remove( x, y, that.playerId );
		}
		
		return false;*/
	}
};

wa1.components.Board.prototype.renderGrid = function() {
	var ctx = this.canvas.getContext('2d');
	var CELL_SIZE = this.CELL_SIZE;
	
	ctx.strokeStyle = '#D8D8D8';
	ctx.beginPath();
	
	// vertical lines
	for(i = 1; i < this.width / CELL_SIZE; i++) {
		ctx.moveTo(i * CELL_SIZE, 0);
		ctx.lineTo(i * CELL_SIZE, this.height);
	}	
	
	// horizontal lines
	for(y = 1; y < this.height / CELL_SIZE; y++) {
		ctx.moveTo(0, y * CELL_SIZE);
		ctx.lineTo(this.width, y * CELL_SIZE);		
	}
	ctx.stroke();
};

wa1.components.Board.prototype.getLifeOnBoardCount = function(playerId) {
	var count = 0;
	
	for(var x = 0; x < this.x_count; x++) {
		for(var y = 0; y < this.y_count; y++) {
			if(this.cells[x][y] == playerId) {
				count++;
			}
		}
	}
	
	return count;
};

wa1.components.Board.prototype.popMoves = function() {
	var retMoves = this.moves;
	this.moves = new Array();
	return retMoves;
};

wa1.components.Board.prototype.step = function() {
	var simulation = new wa1.controllers.Simulation( this.cells, this.playerId, this.enemyId );
	
	var moves = simulation.step();
	//console.log(moves);
	this.executeMoves( moves );
};

wa1.components.Board.prototype.executeMoves = function(moves) {
	that = this;
	goog.array.forEach(moves, function(move) {
		switch(move.move) {
			case "put":
				that.put(move.x,move.y, move.player);
				//this.board[move.x][move.y] = move.player;
			break;
			
			case "remove":
				// TODO: unnamed constant!
				that.remove(move.x, move.y);
				//this.board[move.x][move.y] = 0;
			break;
			
			default:
				throw "Unknown move: " + move.move;
			break;
		}
	});
};

wa1.components.Board.prototype.getMoves = function() {
	
	for(var x = 0; x < this.x_count; x++) {
		for(var y = 0; y < this.y_count; y++) {
			if(this.cells[x][y] != undefined) {
				var move = {x: x, y:y, playerId: this.cells[x][y]};
				console.log( move );
			}
		}
	}
	
};

/** 
	Puts cell on given coordinates
	@param {!number} x
	@param {!number} y
*/
wa1.components.Board.prototype.put = function(x,y, playerId) {	
	if(playerId == undefined) throw "PlayerId must be defined!";
	
	var color = undefined;
	if(playerId == this.playerId) {
		color = "#0000FF";
	}else {
		color = "#FF0000";
	}
	
	this.cells[x][y] = playerId;
	this.fillCell(x, y, color );
};

wa1.components.Board.prototype.remove = function(x,y) {
	this.cells[x][y] = 0;
	this.fillCell(x,y, '#FFFFFF');
	this.renderGrid();	// TODO: performance drawback?
}

/** @private */
wa1.components.Board.prototype.fillCell = function(x,y,color) {
	var CELL_SIZE = this.CELL_SIZE;
	
	var ctx = this.canvas.getContext('2d');
	ctx.fillStyle = color;
	ctx.fillRect(this.map(x), this.map(y), CELL_SIZE, CELL_SIZE);	
}

/** 
	Maps given coordinates to canvas coordinate space
*/
wa1.components.Board.prototype.map = function(coord) {
	coord = coord * this.CELL_SIZE;
	return coord;
};

/** 
	Unmaps given coordinates to board coordinate space
*/
wa1.components.Board.prototype.unmap = function(coord) {
	//console.log(coord + "," + coord / this.CELL_SIZE + ";");
	coord = Math.floor(coord / this.CELL_SIZE);
	return coord;
};
