import cv2
import sys
import easyocr
import numpy as np
import json

# ----------------------------
# Read image sent from Laravel
# ----------------------------
image_path = sys.argv[1]
image = cv2.imread(image_path)

if image is None:
    # If the image failed to load, return default JSON
    print(json.dumps({"plate": "", "bbox": [0, 0, 0, 0]}))
    sys.exit(0)

# ----------------------------
# Preprocess image for OCR
# ----------------------------
# Resize for consistent OCR
scale_width = 800
scale_height = int(image.shape[0] * scale_width / image.shape[1])
image = cv2.resize(image, (scale_width, scale_height))

# Convert to grayscale
gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

# Enhance contrast
gray = cv2.convertScaleAbs(gray, alpha=1.5, beta=0)

# Optional threshold to sharpen text
_, thresh = cv2.threshold(gray, 120, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

# ----------------------------
# Initialize EasyOCR reader
# ----------------------------
# Disable verbose to avoid UnicodeEncodeError in Windows
reader = easyocr.Reader(['en'], verbose=False)

# Detect text
results = reader.readtext(thresh)

# ----------------------------
# Process OCR results
# ----------------------------
plate_text = ""
bbox_coords = [0, 0, 0, 0]  # default

for (bbox, text, prob) in results:
    # Keep alphanumeric characters only
    cleaned = ''.join(filter(str.isalnum, text))
    if 4 <= len(cleaned) <= 10:  # typical plate length
        plate_text = cleaned

        # bbox is four points: [[x1,y1],[x2,y2],[x3,y3],[x4,y4]]
        x_min = min([point[0] for point in bbox])
        y_min = min([point[1] for point in bbox])
        x_max = max([point[0] for point in bbox])
        y_max = max([point[1] for point in bbox])
        bbox_coords = [int(x_min), int(y_min), int(x_max - x_min), int(y_max - y_min)]
        break

# ----------------------------
# Return JSON with plate and bounding box
# ----------------------------
print(json.dumps({"plate": plate_text, "bbox": bbox_coords}))
