# Puzzle Game

## How to Run

This game uses ES6 modules and must be served over HTTP (not opened as a file).

### Option 1: Python HTTP Server (Recommended)
1. Open a terminal/command prompt
2. Navigate to the `puzzle` folder:
   ```
   cd puzzle
   ```
3. Run Python's HTTP server:
   ```
   python -m http.server 8000
   ```
   (Or use `python3` on Mac/Linux)
4. Open your browser and go to:
   ```
   http://localhost:8000
   ```

### Option 2: Node.js HTTP Server
If you have Node.js installed:
```
npx http-server puzzle -p 8000
```
Then open: `http://localhost:8000`

### Option 3: VS Code Live Server
If you use VS Code, install the "Live Server" extension and right-click on `index.html` → "Open with Live Server"

## Troubleshooting

- **Blank screen**: Make sure you're accessing via `http://localhost:8000`, not `file://`
- **Console errors**: Open browser DevTools (F12) and check the Console tab for errors
- **Images not loading**: Verify the `images` folder structure is correct

