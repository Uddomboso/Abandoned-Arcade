// base game runner class
// provides core game loop functionality with frame rate control and pause support
// all game classes should extend this to get animation loop capabilities
export default class GameRunner {
  constructor() {
    // flag to track if game loop is currently running
    this.looping = false
    // flag to track if assets have been preloaded
    this.preloaded = false
    // target frame rate in frames per second
    this.targetFrameRate = 60
    // counter for total frames rendered since start
    this.frameCount = 0
    // current actual frame rate being achieved
    this.frameRate = 0
    // flag to pause game loop
    this.paused = false
    // number of frames to step when paused (for debugging)
    this.stepFrames = null
    // timestamp of last frame for frame rate calculation
    this._lastFrameTime = window.performance.now()

    // store bound loop function to avoid creating new function on each frame
    // this improves performance by reusing the same function reference
    this.__loop = this._loop.bind(this)
  }

  // start the game loop
  // loads assets if needed and begins animation frame requests
  // paused parameter allows starting in paused state
  async start(paused = false) {
    // preload assets on first start
    if (!this.preloaded) {
      // call preload method if it exists (implemented by child classes)
      if (this.preload) {
        await this.preload()
      }
      this.preloaded = true
    }

    // set paused state if requested
    if (paused) {
      this.paused = paused
    }

    // mark loop as active
    this.looping = true

    // begin animation loop if not paused
    if (!paused) {
      window.requestAnimationFrame(this.__loop)
    }
  }

  // stop the game loop completely
  // prevents further frame updates
  stop() {
    this.looping = false
  }

  // pause the game loop
  // game state is preserved but no updates occur
  pause() {
    this.paused = true
  }

  // resume the game loop from paused state
  unpause() {
    this.paused = false
  }

  // step forward a specific number of frames while paused
  // useful for debugging or frame by frame analysis
  step(frames = 1) {
    // accumulate step frames if already stepping
    if (typeof this.stepFrames === 'number') {
      this.stepFrames += frames
    } else {
      this.stepFrames = frames
    }

    // execute loop immediately with current timestamp
    this.__loop(window.performance.now())
  }

  // internal game loop function
  // handles frame timing and calls onFrame method
  // uses requestAnimationFrame for smooth animation
  _loop(timestamp) {
    const now = window.performance.now()
    // calculate time since last frame
    const timeSinceLast = now - this._lastFrameTime
    // calculate target time between frames for desired frame rate
    const targetTimeBetweenFrames = 1000 / this.targetFrameRate

    // only update if enough time has passed (frame rate limiting)
    // 5ms tolerance to account for timing variations
    if (timeSinceLast >= targetTimeBetweenFrames - 5) {
      // call frame update method (implemented by child classes)
      this.onFrame()
      // calculate actual frame rate achieved
      this.frameRate = 1000 / (now - this._lastFrameTime)
      // update last frame time
      this._lastFrameTime = now
      // increment frame counter
      this.frameCount++
    }

    // continue loop if still active
    if (this.looping) {
      let shouldLoop = true

      // handle paused state
      if (this.paused) {
        // if stepping frames, continue until steps complete
        if (typeof this.stepFrames === 'number') {
          if (this.stepFrames === 0) {
            // no more steps, stop looping
            this.stepFrames = null
            shouldLoop = false
          } else {
            // decrement step counter
            this.stepFrames--
          }
        } else {
          // paused and not stepping, stop loop
          shouldLoop = false
        }
      }

      // request next animation frame if should continue
      if (shouldLoop) {
        window.requestAnimationFrame(this.__loop)
      }
    }
  }
}
