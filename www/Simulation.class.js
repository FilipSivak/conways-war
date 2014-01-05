goog.provide("wa1.controllers.Simulation");

/** 
	@constructor
*/
wa1.controllers.Simulation = function(board, playerId, enemyId) {
	this.board = board;
	
	this.x_count = this.board.length;
	this.y_count = this.board[0].length;
	
	this.playerId = playerId;
	this.enemyId = enemyId;
};

wa1.controllers.Simulation.prototype.step = function() {
	var moves = new Array();
	
	for(var x = 0; x < this.x_count; x++) {
		for(var y = 0; y < this.y_count; y++) {
			var p = this.get(x, y);
			if(p == 0) p = undefined;
			
			// 1. Any live cell with fewer than two live neighbours dies, as if caused by under-population.
			if((p != undefined) && (this.getPlayerNeighbourCount(x, y) < 2)) {
				var remove = {x:x, y:y, move: "remove", reason: "underpopulation"};
				moves.push( remove );
				continue;
			}
			
			// 2. Any live cell with two or three live neighbours lives on to the next generation.
			// no change
			
			// 3. Any live cell with more than three live neighbours dies, as if by overcrowding
			if((p != undefined) && (this.getNeighbourCount(x, y) > 3)) {
				var remove = {x:x, y:y, move: "remove", reason: "overcrowd"};
				moves.push( remove );
				continue;
			}
			
			// 4. Any dead cell (or enemy cell) with exactly three live neighbours becomes a live cell, as if by reproduction.			
			var countA = this.getNeighbourCount(x, y, this.playerId);
			var countB = this.getNeighbourCount(x, y, this.enemyId);
			
			if((countA > countB)&&(countA == 3)) {
				var move = {x:x,y:y, move: "put", player: this.playerId};
				moves.push(move);
			}else if((countB > countA)&&(countB == 3)) {
				var move = {x:x,y:y, move: "put", player: this.enemyId};
				moves.push(move);
			}else if((p != undefined)&&(countA == countB)&&(countA == 3)) {
				var move = {x:x,y:y, move: "remove", reason:"killedByEnemy"};
				moves.push(move);
			}
		}
	}
	
	return moves;
};

wa1.controllers.Simulation.prototype.getPlayerNeighbourCount = function(x,y) {
	var player = this.get(x,y);
	return this.getNeighbourCount(x,y, player);
};

wa1.controllers.Simulation.prototype.getNeighbourCount = function(x, y, player) {
		// neighbours clockWise
		
		// TODO: neeedless code?
		/*if((x < 0)||(x >= SIZE))
			throw "X out of range: " + x;
		if((y < 0) || (y >= SIZE))
			throw "Y out of range: " + y;*/
		
		var count = 0;
		goog.array.forEach(this.getNeighbours(x,y), function(p) {
			if(p == 0) p = undefined;
			
			if(player == undefined) {
				if(p != undefined) count++;
			}else if((p != undefined) && (p == player)) {
				count++;
			}
		});
		
		return count;
	}

wa1.controllers.Simulation.prototype.getNeighbours = function(x,y) {
	
	neighs = new Array();
	
	var left = this.get(x-1,y);
	var leftTop = this.get(x-1,y-1);
	var top = this.get(x,y-1);
	var rightTop = this.get(x+1,y-1);
	var right = this.get(x+1,y);
	var rightBottom = this.get(x+1,y+1);
	var bottom = this.get(x,y+1);
	var leftBottom = this.get(x-1,y+1);
	
	neighs.push(left);
	neighs.push(leftTop);
	neighs.push(top);
	neighs.push(rightTop);
	neighs.push(right);
	neighs.push(rightBottom);
	neighs.push(bottom);
	neighs.push(leftBottom);
	
	return neighs;
	
};

wa1.controllers.Simulation.prototype.get = function(x,y) { 
	if(this.board[x] == undefined) return undefined;
	
	return this.board[x][y]; 
}
