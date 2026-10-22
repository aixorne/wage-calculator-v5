<?php
declare(strict_types=1);

/*
 * OCR.Space configuration for Render.
 * Render Dashboard → your Web Service → Environment:
 *   OCR_SPACE_API_KEY = your current OCR.Space API key
 * Never commit or share the API key in source code.
 */
$OCR_API_KEY = trim((string)(getenv('OCR_SPACE_API_KEY') ?: ''));
