// chrome dino game main class
// implements the complete game logic including rendering, collision detection, and difficulty progression
// extends GameRunner to get animation loop functionality

import Bird from '../actors/Bird.js'
import Cactus from '../actors/Cactus.js'
import Cloud from '../actors/Cloud.js'
import Dino from '../actors/Dino.js'
import sprites from '../sprites.js'
import { playSound } from '../sounds.js'
import {
  loadFont,
  loadImage,
  getImageData,
  randBoolean,
  randInteger,
} from '../utils.js'
import GameRunner from './GameRunner.js'

export default class DinoGame extends GameRunner {
  // initialize game with canvas dimensions
  // sets up default game settings and initial state
  constructor(width, height) {
    super()

    // canvas dimensions (set after creation)
    this.width = null
    this.height = null
    // create and configure canvas element
    this.canvas = this.createCanvas(width, height)
    // get 2d rendering context for drawing
    this.canvasCtx = this.canvas.getContext('2d')
    // sprite sheet image (loaded in preload)
    this.spriteImage = null
    // processed image data for sprite extraction
    this.spriteImageData = null

    // default game settings
    // units: fpa = frames per action, ppf = pixels per frame, px = pixels
    this.defaultSettings = {
      bgSpeed: 8, // ppf
      birdSpeed: 7.2, // ppf
      birdSpawnRate: 240, // fpa
      birdWingsRate: 15, // fpa
      cactiSpawnRate: 50, // fpa
      cloudSpawnRate: 200, // fpa
      cloudSpeed: 2, // ppf
      dinoGravity: 0.5, // ppf
      dinoGroundOffset: 4, // px
      dinoLegsRate: 6, // fpa
      dinoLift: 10, // ppf
      scoreBlinkRate: 20, // fpa
      scoreIncreaseRate: 6, // fpa
    }

    this.state = {
      settings: { ...this.defaultSettings },
      birds: [],
      cacti: [],
      clouds: [],
      dino: null,
      gameOver: false,
      groundX: 0,
      groundY: 0,
      isRunning: false,
      level: 0,
      score: {
        blinkFrames: 0,
        blinks: 0,
        isBlinking: false,
        value: 0,
      },
    }
  }

  // create canvas element with proper pixel density handling
  // accounts for high dpi displays to prevent blurry rendering
  // reference: https://developer.mozilla.org/en-US/docs/Web/API/Window/devicePixelRatio#correcting_resolution_in_a_%3Ccanvas%3E
  createCanvas(width, height) {
    const canvas = document.createElement('canvas')
    const ctx = canvas.getContext('2d')
    const scale = window.devicePixelRatio

    this.width = width
    this.height = height
    canvas.style.width = width + 'px'
    canvas.style.height = height + 'px'
    canvas.width = Math.floor(width * scale)
    canvas.height = Math.floor(height * scale)
    ctx.scale(scale, scale)

    document.body.appendChild(canvas)
    return canvas
  }

  // preload game assets before starting
  // loads sprite sheet image and custom font in parallel
  // initializes dino character with starting position and physics
  async preload() {
    const { settings } = this.state
    // load assets in parallel for faster initialization
    const [spriteImage] = await Promise.all([
      loadImage('./assets/sprite.png'),
      loadFont('./assets/PressStart2P-Regular.ttf', 'PressStart2P'),
    ])
    // store loaded sprite image
    this.spriteImage = spriteImage
    // extract image data for sprite operations
    this.spriteImageData = getImageData(spriteImage)
    // create dino character instance
    const dino = new Dino(this.spriteImageData)

    // configure dino physics and animation
    dino.legsRate = settings.dinoLegsRate
    dino.lift = settings.dinoLift
    dino.gravity = settings.dinoGravity
    // set starting position
    dino.x = 25
    dino.baseY = this.height - settings.dinoGroundOffset
    // store dino in game state
    this.state.dino = dino
    // calculate ground y position for rendering
    this.state.groundY = this.height - sprites.ground.h / 2
  }

  // main frame update method called every frame
  // handles rendering and game logic updates
  onFrame() {
    const { state } = this

    // draw static elements first (background, ground, clouds, dino, score)
    this.drawBackground()
    // fps display can be enabled for debugging
    // this.drawFPS()
    this.drawGround()
    this.drawClouds()
    this.drawDino()
    this.drawScore()

    // only update game logic if game is running
    if (state.isRunning) {
      // draw obstacles
      this.drawCacti()

      // birds only appear after level 3 for increased difficulty
      if (state.level > 3) {
        this.drawBirds()
      }

      // check collision with first obstacle (cactus or bird)
      // only check first obstacle for performance
      if (state.dino.hits([state.cacti[0], state.birds[0]])) {
        // collision detected, end game
        playSound('game-over')
        state.gameOver = true
        state.isRunning = false
        this.endGame()
        // stop processing this frame after collision
        return
      }

      // update score if game is still active
      if (!state.gameOver) {
        this.updateScore()
      }
    } 
    // draw game over screen if game ended
    else if (state.gameOver) {
      this.endGame()
    }
  }

  // handle user input events
  // processes jump, duck, and restart commands
  onInput(type) {
    const { state } = this

    switch (type) {
      case 'jump': {
        // if game is running, make dino jump
        if (state.isRunning) {
          // jump returns true if jump was successful (not already jumping)
          if (state.dino.jump()) {
            playSound('jump')
          }
        } 
        // if game over, restart and jump
        else if (state.gameOver) {
          // set gameOver to false first to prevent flicker
          state.gameOver = false
          this.resetGame()
          state.isRunning = true
          state.dino.jump()
          playSound('jump')
        } 
        // if game not started, start it and jump
        else {
          state.isRunning = true
          state.dino.jump()
          playSound('jump')
        }
        break
      }

      case 'duck': {
        // if game running, make dino duck
        if (state.isRunning) {
          state.dino.duck(true)
        } 
        // if game not started and not over, start game
        else if (!state.gameOver) {
          state.isRunning = true
        }
        break
      }

      case 'stop-duck': {
        // release duck when key is released
        if (state.isRunning) {
          state.dino.duck(false)
        }
        break
      }
      
      case 'restart': {
        // restart from game over state
        if (state.gameOver) {
          // set gameOver to false immediately to prevent flicker
          state.gameOver = false
          // reset all game state
          this.resetGame()
          // start immediately after reset
          state.isRunning = true
        } 
        // start game if not running and not over
        else if (!state.isRunning && !state.gameOver) {
          state.isRunning = true
        }
        break
      }
    }
  }

  // reset game to initial state
  // clears all obstacles, resets score, and prepares for new game
  resetGame() {
    // set gameOver to false first to prevent any flicker during reset
    this.state.gameOver = false
    
    // reset dino to starting position and state
    this.state.dino.reset()
    
    // clear all obstacles immediately to prevent rendering issues
    this.state.birds = []
    this.state.cacti = []
    this.state.clouds = []
    
    // reset all game state to defaults
    Object.assign(this.state, {
      // restore default settings
      settings: { ...this.defaultSettings },
      // ensure game over flag is false
      gameOver: false,
      // start paused, wait for user input to begin
      isRunning: false,
      // reset level to 0
      level: 0,
      // reset ground scroll position
      groundX: 0,
      // reset score and blink state
      score: {
        blinkFrames: 0,
        blinks: 0,
        isBlinking: false,
        value: 0,
      },
    })

    // reset frame counter for consistent timing
    this.frameCount = 0
    
    // ensure game loop is running (in case it was stopped)
    if (!this.looping) {
    this.start()
    }
  }

  // display game over screen
  // shows game over text and replay icon
  // does not stop game loop to allow restart functionality
  endGame() {
    // only show game over screen if game is actually over
    // prevents flicker when restarting
    if (!this.state.gameOver) {
      return
    }

    const iconSprite = sprites.replayIcon
    const padding = 15

    // draw game over text centered on screen
    this.paintText(
      'G A M E  O V E R',
      this.width / 2,
      this.height / 2 - padding,
      {
        font: 'PressStart2P',
        size: '12px',
        align: 'center',
        baseline: 'bottom',
        color: '#535353',
      }
    )

    // draw replay icon below text
    this.paintSprite(
      'replayIcon',
      this.width / 2 - iconSprite.w / 4,
      this.height / 2 - iconSprite.h / 4 + padding
    )

    // draw final score
    this.drawScore()
    // note: do not stop the loop, keep it running so restart works
  }

  // increase game difficulty as player progresses
  // adjusts speed, spawn rates, and animation rates based on level
  increaseDifficulty() {
    const { birds, cacti, clouds, dino, settings } = this.state
    const { bgSpeed, cactiSpawnRate, dinoLegsRate } = settings
    const { level } = this.state

    // gradual speed increase starting from level 1 (100 points)
    if (level >= 1) {
      // calculate speed increase: base speed + (level * 0.3)
      // this makes speed increase more noticeable per level
      const speedIncrease = level * 0.3
      // cap maximum speed at 20 pixels per frame
      settings.bgSpeed = Math.min(this.defaultSettings.bgSpeed + speedIncrease, 20)
      // bird speed is 90% of background speed
      settings.birdSpeed = settings.bgSpeed * 0.9
      
      // gradually decrease spawn rates to make obstacles more frequent
      // starts at level 2 to give player time to adjust
      if (level >= 2) {
        // reduce spawn rate by 2% per level, minimum 30 frames
        settings.cactiSpawnRate = Math.max(
          Math.floor(this.defaultSettings.cactiSpawnRate * (1 - level * 0.02)),
          30
        )
      }
      
      // increase dino animation speed (legs move faster)
      // starts at level 3, reduces leg animation frames
      if (level >= 3 && dinoLegsRate > 3) {
        // reduce leg rate by 1 every 3 levels, minimum 3 frames
        settings.dinoLegsRate = Math.max(this.defaultSettings.dinoLegsRate - Math.floor(level / 3), 3)
      }
    }

    // update existing obstacles to new speed immediately
    // ensures all obstacles on screen match new difficulty
    for (const bird of birds) {
      bird.speed = settings.birdSpeed
    }

    for (const cactus of cacti) {
      cactus.speed = settings.bgSpeed
    }

    for (const cloud of clouds) {
      cloud.speed = settings.cloudSpeed || settings.bgSpeed * 0.25
    }

    // update dino leg animation rate
    dino.legsRate = settings.dinoLegsRate
  }

  // update player score and handle level progression
  // increases score every few frames and checks for level up
  updateScore() {
    const { state } = this

    // only update score at configured rate (every 6 frames by default)
    if (this.frameCount % state.settings.scoreIncreaseRate === 0) {
      const oldLevel = state.level

      // increment score
      state.score.value++
      // level increases every 100 points
      state.level = Math.floor(state.score.value / 100)

      // check if level changed
      if (state.level !== oldLevel) {
        // level up: play sound, increase difficulty, blink score
        playSound('level-up')
        this.increaseDifficulty()
        state.score.isBlinking = true
      } else {
        // between levels, gradually increase speed for smoother progression
        // every 20 points, add small speed boost
        if (state.score.value % 20 === 0 && state.level > 0) {
          const speedBoost = 0.05
          // cap speed at 20 pixels per frame
          state.settings.bgSpeed = Math.min(state.settings.bgSpeed + speedBoost, 20)
          state.settings.birdSpeed = state.settings.bgSpeed * 0.9
          
          // update existing obstacles to new speed
          state.birds.forEach(bird => bird.speed = state.settings.birdSpeed)
          state.cacti.forEach(cactus => cactus.speed = state.settings.bgSpeed)
          state.clouds.forEach(cloud => cloud.speed = state.settings.cloudSpeed || state.settings.bgSpeed * 0.25)
        }
      }
    }
  }

  drawFPS() {
    this.paintText('fps: ' + Math.round(this.frameRate), 0, 0, {
      font: 'PressStart2P',
      size: '12px',
      baseline: 'top',
      align: 'left',
      color: '#535353',
    })
  }

  drawBackground() {
    this.canvasCtx.fillStyle = '#f7f7f7'
    this.canvasCtx.fillRect(0, 0, this.width, this.height)
  }

  // draw scrolling ground texture
  // creates seamless infinite scrolling effect
  drawGround() {
    const { state } = this
    const { bgSpeed } = state.settings
    // ground sprite is drawn at half size, so width is divided by 2
    const groundImgWidth = sprites.ground.w / 2

    // draw first ground segment
    this.paintSprite('ground', state.groundX, state.groundY)
    
    // only scroll ground if game is running
    if (state.isRunning) {
    state.groundX -= bgSpeed
    }

    // when first segment is partially off screen, draw second segment
    // creates seamless loop effect
    if (state.groundX <= -groundImgWidth + this.width) {
      this.paintSprite('ground', state.groundX + groundImgWidth, state.groundY)

      // when first segment fully off screen, reset position
      if (state.groundX <= -groundImgWidth) {
        state.groundX = -bgSpeed
      }
    }
  }

  drawClouds() {
    const { clouds, settings, isRunning } = this.state

    // Only progress clouds if game is running
    if (isRunning) {
    this.progressInstances(clouds)
    if (this.frameCount % settings.cloudSpawnRate === 0) {
      const newCloud = new Cloud()
        newCloud.speed = settings.cloudSpeed || settings.bgSpeed * 0.25
      newCloud.x = this.width
      newCloud.y = randInteger(20, 80)
      clouds.push(newCloud)
      }
    }
    this.paintInstances(clouds)
  }

  drawDino() {
    const { dino } = this.state

    dino.nextFrame()
    this.paintSprite(dino.sprite, dino.x, dino.y)
  }

  drawCacti() {
    const { state } = this
    const { cacti, settings, isRunning } = state

    // Only progress cacti if game is running
    if (isRunning) {
    this.progressInstances(cacti)
    if (this.frameCount % settings.cactiSpawnRate === 0) {
      // randomly either do or don't add cactus
      if (!state.birds.length && randBoolean()) {
        const newCacti = new Cactus(this.spriteImageData)
        newCacti.speed = settings.bgSpeed
        newCacti.x = this.width
        newCacti.y = this.height - newCacti.height - 2
        cacti.push(newCacti)
        }
      }
    }
    this.paintInstances(cacti)
  }

  drawBirds() {
    const { birds, settings, isRunning } = this.state

    // Only progress birds if game is running
    if (isRunning) {
    this.progressInstances(birds)
    if (this.frameCount % settings.birdSpawnRate === 0) {
      // randomly either do or don't add bird
      if (randBoolean()) {
        const newBird = new Bird(this.spriteImageData)
        newBird.speed = settings.birdSpeed
        newBird.wingsRate = settings.birdWingsRate
        newBird.x = this.width
        // ensure birds are always at least 5px higher than a ducking dino
        newBird.y =
          this.height -
          Bird.maxBirdHeight -
          Bird.wingSpriteYShift -
          5 -
          sprites.dinoDuckLeftLeg.h / 2 -
          settings.dinoGroundOffset
        birds.push(newBird)
        }
      }
    }
    this.paintInstances(birds)
  }

  drawScore() {
    const { canvasCtx, state } = this
    const { isRunning, score, settings } = state
    const fontSize = 12
    let shouldDraw = true
    let drawValue = score.value

    if (isRunning && score.isBlinking) {
      score.blinkFrames++

      if (score.blinkFrames % settings.scoreBlinkRate === 0) {
        score.blinks++
      }

      if (score.blinks > 7) {
        score.blinkFrames = 0
        score.blinks = 0
        score.isBlinking = false
      } else {
        if (score.blinks % 2 === 0) {
          drawValue = Math.floor(drawValue / 100) * 100
        } else {
          shouldDraw = false
        }
      }
    }

    if (shouldDraw) {
      // draw the background behind it in case this is called
      // at a time where the background isn't re-drawn (i.e. in `endGame`)
      canvasCtx.fillStyle = '#f7f7f7'
      canvasCtx.fillRect(this.width - fontSize * 5, 0, fontSize * 5, fontSize)

      this.paintText((drawValue + '').padStart(5, '0'), this.width, 0, {
        font: 'PressStart2P',
        size: `${fontSize}px`,
        align: 'right',
        baseline: 'top',
        color: '#535353',
      })
    }
  }

  // update all instances in array and remove off screen ones
  // iterates backwards to safely remove items during iteration
  // for each instance: calculate next frame position, remove if off screen
  progressInstances(instances) {
    for (let i = instances.length - 1; i >= 0; i--) {
      const instance = instances[i]

      // update instance position and animation
      instance.nextFrame()
      // remove if completely off left side of screen
      if (instance.rightX <= 0) {
        instances.splice(i, 1)
      }
    }
  }

  // draw all instances in array to canvas
  // renders each actor at its current position
  paintInstances(instances) {
    for (const instance of instances) {
      this.paintSprite(instance.sprite, instance.x, instance.y)
    }
  }

  // draw sprite from sprite sheet to canvas
  // extracts sprite from sprite sheet and draws at specified position
  // sprites are drawn at half size (w/2, h/2) for pixel art scaling
  paintSprite(spriteName, dx, dy) {
    const { h, w, x, y } = sprites[spriteName]
    // drawImage: source image, source x/y/w/h, destination x/y/w/h
    this.canvasCtx.drawImage(this.spriteImage, x, y, w, h, dx, dy, w / 2, h / 2)
  }

  // draw text to canvas with custom styling
  // supports font, size, alignment, baseline, and color options
  paintText(text, x, y, opts) {
    const { font = 'serif', size = '12px' } = opts
    const { canvasCtx } = this

    // set font properties
    canvasCtx.font = `${size} ${font}`
    if (opts.align) canvasCtx.textAlign = opts.align
    if (opts.baseline) canvasCtx.textBaseline = opts.baseline
    if (opts.color) canvasCtx.fillStyle = opts.color
    // draw text at specified position
    canvasCtx.fillText(text, x, y)
  }
}
