<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>The King's Escape</title>
  <style>
    body { font-family: Arial; text-align: center; }
    h1 { margin-top: 20px; }
    #board {
      display: grid;
      grid-template-columns: repeat(8, 60px);
      grid-template-rows: repeat(8, 60px);
      gap: 0;
      margin: 20px auto;
      border: 3px solid #333;
      width: 480px;
      height: 480px;
    }
    .cell {
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      cursor: pointer;
    }
    .white { background: #eee; }
    .black { background: #555; color: white; }
    .selected { outline: 3px solid yellow; }
    #message { font-size: 18px; font-weight: bold; margin-top: 15px; }
    button { margin-top: 10px; padding: 8px 15px; font-size: 16px; }
  </style>
</head>
<body>
<h1>♔ The King's Escape ♔</h1>
<p>Guide the King from (1,1) to the exit (8,8) while avoiding enemies.</p>

<div id="board"></div>
<div id="message"></div>
<button onclick="resetGame()">↺ Reset</button>

<script>
const boardSize = 8;
const boardEl = document.getElementById("board");
const messageEl = document.getElementById("message");

let kingPos = {x:0, y:7};
const goalPos = {x:7, y:0};
let selected = false;

// Enemies: type = "pawn" or "bishop"
let enemies = [
  {x:3, y:3, type:"pawn"},
  {x:5, y:2, type:"bishop"}
];

// Draw the board
function drawBoard() {
  boardEl.innerHTML = "";
  for (let y = 0; y < boardSize; y++) {
    for (let x = 0; x < boardSize; x++) {
      const cell = document.createElement("div");
      cell.classList.add("cell", (x+y)%2===0?"white":"black");

      // Place King
      if (kingPos.x===x && kingPos.y===y) {
        cell.textContent = "♔";
        if (selected) cell.classList.add("selected");
      }
      // Place Goal
      else if (goalPos.x===x && goalPos.y===y) {
        cell.textContent = "🏰";
      }
      // Place Enemies
      else {
        const enemy = enemies.find(e => e.x===x && e.y===y);
        if (enemy) {
          cell.textContent = (enemy.type==="pawn") ? "♟" : "♝";
        }
      }

      cell.addEventListener("click", ()=>handleClick(x,y));
      boardEl.appendChild(cell);
    }
  }
}

// King click handler
function handleClick(x,y) {
  if (!selected) {
    if (kingPos.x===x && kingPos.y===y) {
      selected = true;
      drawBoard();
    }
  } else {
    if (isValidMove(x,y)) {
      kingPos = {x,y};
      selected = false;
      checkGameOver();
      enemyMoves();
      checkGameOver();
      drawBoard();
    } else {
      messageEl.textContent = "❌ Invalid move!";
    }
  }
}

// King can move 1 square any direction
function isValidMove(x,y) {
  const dx = Math.abs(x-kingPos.x);
  const dy = Math.abs(y-kingPos.y);
  if ((dx<=1 && dy<=1) && !(x===kingPos.x && y===kingPos.y)) {
    if (enemies.some(e=>e.x===x && e.y===y)) return false;
    return true;
  }
  return false;
}

// Enemy movement
function enemyMoves() {
  enemies.forEach(enemy=>{
    if (enemy.type==="pawn") {
      // simple: move one step diagonally towards king if possible
      let dirs = [{dx:-1,dy:1},{dx:1,dy:1}]; // downward direction
      for (let d of dirs) {
        let nx = enemy.x + d.dx;
        let ny = enemy.y + d.dy;
        if (nx>=0 && nx<boardSize && ny>=0 && ny<boardSize) {
          // move if square empty
          if (!isOccupied(nx,ny)) { enemy.x=nx; enemy.y=ny; break; }
        }
      }
    } else if (enemy.type==="bishop") {
      // move diagonally randomly one step
      const moves = [{dx:1,dy:1},{dx:-1,dy:1},{dx:1,dy:-1},{dx:-1,dy:-1}];
      for (let i=0;i<moves.length;i++) {
        const m = moves[Math.floor(Math.random()*moves.length)];
        let nx = enemy.x + m.dx;
        let ny = enemy.y + m.dy;
        if (nx>=0 && nx<boardSize && ny>=0 && ny<boardSize) {
          if (!isOccupied(nx,ny)) { enemy.x=nx; enemy.y=ny; break; }
        }
      }
    }
  });
}

// check if a square is occupied by king or enemy
function isOccupied(x,y) {
  if (kingPos.x===x && kingPos.y===y) return true;
  if (enemies.some(e=>e.x===x && e.y===y)) return true;
  return false;
}

// Check for game over / win
function checkGameOver() {
  // king captured
  if (enemies.some(e=>e.x===kingPos.x && e.y===kingPos.y)) {
    messageEl.textContent = "💀 The King was captured! Game Over.";
    selected=false;
  }
  // king escapes
  else if (kingPos.x===goalPos.x && kingPos.y===goalPos.y) {
    messageEl.textContent = "🎉 You Win! The King escaped!";
  }
}

// Reset
function resetGame() {
  kingPos = {x:0, y:7};
  selected = false;
  enemies = [
    {x:3, y:3, type:"pawn"},
    {x:5, y:2, type:"bishop"}
  ];
  messageEl.textContent = "";
  drawBoard();
}

drawBoard();
</script>
</body>
</html>
