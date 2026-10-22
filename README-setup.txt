WAGE CALCULATOR — OCR ERROR DIAGNOSTICS

Files in this package:
- index.php: UI; shows useful HTTP/provider error details.
- ocr.php: validates uploads, calls OCR.Space, returns JSON errors, and writes safe diagnostics to Render logs.
- config.php: reads OCR_SPACE_API_KEY from the Render environment.

Deploy steps:
1. Replace these files in the same folder in your GitHub repository. Keep any other project files unchanged.
2. In Render → Web Service → Environment, confirm OCR_SPACE_API_KEY exists. Do not put the key in GitHub or send it in chat.
3. Commit/push the files and wait for Render deployment to finish.
4. Test OCR from the website.
5. In Render Logs, search for: [WageCalculator OCR]

Important:
- This package does not include a real API key. Keep your key only in Render Environment.
- OCR.Space requests need outbound internet and PHP cURL.
- If the Render log shows HTTP 502 but no [WageCalculator OCR] entries, the request may be failing before the PHP script can log; inspect the surrounding Render runtime logs and request size.
