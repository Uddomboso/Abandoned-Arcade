// main entry point for the chrome dino game
// handles input events and initializes the game instance
import DinoGame from './game/DinoGame.js'

// create game instance with canvas dimensions 600x150 pixels
const game = new DinoGame(600, 150)

// detect if device supports touch input
// checks multiple touch detection methods for compatibility
const isTouchDevice =
  'ontouchstart' in window ||
  navigator.maxTouchPoints > 0 ||
  navigator.msMaxTouchPoints > 0

// iframe compatibility setup
// when game is embedded in an iframe, ensure it can receive keyboard events
// this is necessary because iframes need explicit focus to capture keyboard input
if (window.self !== window.top) {
  // we're in an iframe, make sure we can receive keyboard events
  window.focus()
  document.body.setAttribute('tabindex', '-1')
  document.body.style.outline = 'none'
}

// handle keyboard key down events
// processes jump, duck, and restart commands from keyboard input
function handleKeyDown(e) {
  // map key codes to game actions
  const keycodes = {
    // up arrow key (38) and spacebar (32) trigger jump
    JUMP: { 38: 1, 32: 1 },
    // down arrow key (40) triggers duck
    DUCK: { 40: 1 },
    // enter key (13) triggers restart
    RESTART: { 13: 1 },
  }
  
  // get key code using legacy or modern api
  const keyCode = e.keyCode || e.which
  const key = e.key
  
  // handle restart on enter key press
  // allows restarting when game is over or not started
  if (keycodes.RESTART[keyCode] || key === 'Enter') {
    e.preventDefault()
    if (game.state && (game.state.gameOver || !game.state.isRunning)) {
      game.onInput('restart')
    }
    return
  }
  
  // handle jump action
  // spacebar or up arrow makes dino jump
  if (keycodes.JUMP[keyCode] || key === 'ArrowUp' || key === ' ') {
    e.preventDefault()
    game.onInput('jump')
  } 
  // handle duck action
  // down arrow makes dino duck
  else if (keycodes.DUCK[keyCode] || key === 'ArrowDown') {
    e.preventDefault()
    game.onInput('duck')
  }
}

// handle keyboard key up events
// releases duck action when down arrow is released
function handleKeyUp(e) {
  const keycodes = {
    DUCK: { 40: 1 },
  }
  
  const keyCode = e.keyCode || e.which
  const key = e.key
  
  // stop ducking when down arrow is released
  if (keycodes.DUCK[keyCode] || key === 'ArrowDown') {
    e.preventDefault()
    game.onInput('stop-duck')
  }
}

// touch device input handling
// provides touch controls for mobile devices
if (isTouchDevice) {
  // single touch triggers jump, double touch triggers duck
  document.addEventListener('touchstart', ({ touches }) => {
    if (touches.length === 1) {
      game.onInput('jump')
    } else if (touches.length === 2) {
      game.onInput('duck')
    }
  })

  // release duck when touch ends
  document.addEventListener('touchend', ({ touches }) => {
    game.onInput('stop-duck')
  })
} else {
  // desktop keyboard input handling
  // listen on multiple targets for better iframe support
  // some browsers require events on window, others on document
  window.addEventListener('keydown', handleKeyDown)
  document.addEventListener('keydown', handleKeyDown)
  
  window.addEventListener('keyup', handleKeyUp)
  document.addEventListener('keyup', handleKeyUp)
  
  // also listen on body element for additional focus support
  document.body.addEventListener('keydown', handleKeyDown)
  document.body.addEventListener('keyup', handleKeyUp)
}

// mouse click handling
// allows clicking to focus game and restart on game over screen
document.body.addEventListener('click', (e) => {
  // ensure window and body have focus for keyboard events
  window.focus()
  document.body.focus()
  
  // check if click is on replay icon area for restart
  const canvas = document.querySelector('canvas')
  if (canvas && game.state) {
    // calculate click position relative to canvas
    const rect = canvas.getBoundingClientRect()
    const x = e.clientX - rect.left
    const y = e.clientY - rect.top
    const centerX = canvas.width / 2
    const centerY = canvas.height / 2
    
    // if game is over and click is near center (replay icon area), restart
    if (game.state.gameOver) {
      // check if click is within replay icon bounds (approximate 100x60 area)
      if (x >= centerX - 50 && x <= centerX + 50 && 
          y >= centerY - 30 && y <= centerY + 30) {
        game.onInput('restart')
      }
    } 
    // if game hasn't started, any click starts it
    else if (!game.state.isRunning) {
      game.onInput('jump')
    }
  }
})

// touch support for restart and game start
// handles touch events for mobile devices to restart or start game
if (isTouchDevice) {
  document.addEventListener('touchstart', (e) => {
    // restart if game is over
    if (game.state && game.state.gameOver) {
      game.onInput('restart')
    } 
    // start game if not running
    else if (game.state && !game.state.isRunning) {
      game.onInput('jump')
    }
  })
}

// start the game
// begins loading assets and initializing game loop
game.start().catch(console.error)
