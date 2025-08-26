<!-- <template>
  <div class="d-flex flex-column align-center justify-center">
    <!-- Camera Preview -->
    <video ref="video" autoplay playsinline width="400"></video>

  
    <div v-if="plateText" class="mt-4 text-h6">
      Detected Plate: {{ plateText }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import Tesseract from "tesseract.js";

const video = ref(null);
const plateText = ref("");
let intervalId = null;

onMounted(async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.value.srcObject = stream;

    // Run OCR every 3 seconds
    intervalId = setInterval(() => scanFrame(), 3000);
  } catch (err) {
    console.error("Camera access denied:", err);
  }
});

onBeforeUnmount(() => {
  if (intervalId) clearInterval(intervalId);
});

const scanFrame = () => {
  const canvas = document.createElement("canvas");
  canvas.width = video.value.videoWidth;
  canvas.height = video.value.videoHeight;
  const ctx = canvas.getContext("2d");
  ctx.drawImage(video.value, 0, 0, canvas.width, canvas.height);

  Tesseract.recognize(canvas.toDataURL("image/png"), "eng").then(({ data: { text } }) => {
    plateText.value = text.trim();
    console.log("Detected Plate:", text);
  });
};
</script> -->
