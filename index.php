<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>A * Path Finding Algorithm</title>
<style>
  html,
body,
div {
  font-family: Arial, sans-serif;
  background: #211f1f;
  border: 0px;
  padding: 0px;
  margin: 0px;
  height: 100%;
  display: block;
}

canvas {
  height: 90%;
  width: 100%;
  margin: auto;
  background-color: #211f1f;
  display: block;
}

#s {
  width: 100%;
  color: white;
  font-size: 25px;
  background: #211f1f;
  text-align: center;
}

#container {
  height: 10%;
  width: 100%;
  background: #211f1f;
  display: flex;
  align-items: center;
  justify-content: center;
}

</style>

</head>
<body>

	<div>
		<canvas id="canvas"></canvas>
		<div id="container">
			<p id="s">A* Path Finding Algorithm: Choose Start and End point. Refresh page to get new canvas or random blocks</p>
		</div>
	</div>
	<script type="text/javascript">
class Node {
	constructor(type){
		this._startDist = 10000000000;  
		this._endDist;  
		this._eval;  
		this._type = type  
		this._parent = { };
	}

	set startDist(distance){
		this._startDist = distance;
		this._eval = this._startDist + this._endDist;
	}
	set endDist(distance){
		this._endDist = distance;
	}
	set type(type){
		this._type = type;
	}
	set parent(nodePos){
		this._parent.x = nodePos.x;
		this._parent.y = nodePos.y;
	}

	get startDist(){
		return this._startDist;
	}
	get endDist(){
		return this._endDist;
	}
	get eval(){
		return this._eval;
	}
	get type(){
		return this._type;
	}
	get parent(){
		return {x:this._parent.x, y:this._parent.y};
	}


}

  </script>
	<script type="text/javascript">
const canvas = document.getElementById('canvas')
const ctx = canvas.getContext('2d')
const tilesSize = 20; // Change me if you want to!
const widthNumber = canvas.scrollWidth/tilesSize;
const heightNumber = canvas.scrollHeight/tilesSize;
let matrix = [];  
let intialize = 0; 
let startNode = { };  //starting point
let endCordinates = { };  //End point
let evaluated = [];  
let checked = [];  

const createMatrix = () => {
	for (var i = 0; i < heightNumber; i++) {
		let helper = [];
  		for (var j = 0; j < widthNumber; j++) {
  			if(j == 0 || j == ~~widthNumber){  
  				helper[j] = new Node(1);
  			}else{
  				helper[j] = new Node(Math.round(Math.random()-0.2));
			}
		}
		matrix[i] = helper;
	}

	matrix[0].map(node => node.type = 1);
	matrix[matrix.length-1].map(node => node.type = 1);
}

const draw = () => {
 	 for (let i = 0; i < heightNumber; i++) {
  		for (let j = 0; j < widthNumber; j++) {
	  		ctx.fillStyle = '#0054fc';
	  		ctx.fillRect(tilesSize*j, tilesSize*i, tilesSize, tilesSize);
  			if(matrix[i][j].type == 0){
  				ctx.fillStyle = 'white';
  				ctx.fillRect(tilesSize*j, tilesSize*i, tilesSize-0.5, tilesSize-0.5);
  			}else if (matrix[i][j].type == 3){
  				ctx.fillStyle = 'green';
  				ctx.fillRect(tilesSize*j, tilesSize*i, tilesSize-0.5, tilesSize-0.5);
  			}
  		}
  	}
}

const distBetweenNodes =  parent => node => {
	return (Math.abs(parent.x - node.x) + Math.abs(parent.y - node.y))*tilesSize;
}

const fillData = y => x => parent => {
	if(!checked.some(node => node.x == x && node.y == y)){
		if(matrix[y][x].type !=  1 && (tilesSize + matrix[parent.y][parent.x].startDist) < matrix[y][x].startDist){
			matrix[y][x].endDist = distBetweenNodes(parent)(endCordinates);
			matrix[y][x].startDist = tilesSize + matrix[parent.y][parent.x].startDist;
			matrix[y][x].parent = parent;
			ctx.fillStyle = '#79e8f7';
			ctx.fillRect(tilesSize*x, tilesSize*y, tilesSize-0.5, tilesSize-0.5);
			evaluated
        .push({x:x, y:y});
		}
	}
}

const evaluete = parent => {
	fillData(parent.y + 1)(parent.x)(parent);
	fillData(parent.y)(parent.x - 1)(parent);
	fillData(parent.y - 1)(parent.x)(parent);
	fillData(parent.y)(parent.x + 1)(parent);
}

const start = () => {

	checked.push(startNode);
	evaluete(startNode);
	if(evaluated
    .length == 0){
		alert("No possible path");
		return;
	}
	var next = evaluated
.reduce((a, b) => matrix[a.y][a.x].eval<matrix[b.y][b.x].eval?a:b);
	evaluated
.splice(evaluated
    .indexOf(next), 1);
	checked.push(next);
	var noPath = false;
	
	while(!(next.x == endCordinates.x && next.y == endCordinates.y)){

		evaluete(next);
		if(evaluated
        .length == 0){
			alert("No possible path");
			noPath = true;
			break;
		}
		var next = evaluated
    .reduce((a, b) => matrix[a.y][a.x].eval<matrix[b.y][b.x].eval?a:b);
		checked.push(next);
		evaluated
    .splice(evaluated
        .indexOf(next), 1);
	}


	ctx.fillStyle = 'red';
	ctx.fillRect(endCordinates.x*tilesSize, endCordinates.y*tilesSize, tilesSize-0.5, tilesSize-0.5);
	var parent = matrix[endCordinates.y][endCordinates.x].parent;
	if(!noPath){
		while(!(parent.x == startNode.x && parent.y == startNode.y)){
			ctx.fillStyle = 'yellow';
			ctx.fillRect(parent.x*tilesSize+0.5, parent.y*tilesSize+0.5, tilesSize-2, tilesSize-2);
			parent = matrix[parent.y][parent.x].parent;
		}
	}
}

const makeStart = (i) => (j) => { 
	if(matrix[i][j].type == 0){
	 	matrix[i][j].type = 3;
	 	matrix[i][j].startDist = 0;
	 	ctx.fillStyle = 'green';
	 	ctx.fillRect(tilesSize*j, tilesSize*i, tilesSize-0.5, tilesSize-0.5);
	 	intialize++;
	 	startNode = {x:j, y:i};
	 }
}
 
const makeFinish = (i) => (j) => { 
	if(matrix[i][j].type == 0 && intialize == 1){
	 	matrix[i][j].type = 5;
	 	ctx.fillStyle = 'red';
	 	ctx.fillRect(tilesSize*j, tilesSize*i, tilesSize-0.5, tilesSize-0.5);
	 	intialize++;
	 	endCordinates = {x:j, y:i};
	 	start();
	}
}

canvas.addEventListener('click', (e) => {
	 const pos = {x: e.clientX, y: e.clientY};
	 let j = Math.floor(pos.x/tilesSize);
	 let i = Math.floor(pos.y/tilesSize);
	 intialize==0?makeStart(i)(j):makeFinish(i)(j);
});

canvas.width = canvas.scrollWidth;
canvas.height = canvas.scrollHeight;
createMatrix();
draw();

  </script>
</body>
</html>