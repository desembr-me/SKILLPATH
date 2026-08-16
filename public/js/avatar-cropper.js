/**
 * SkillPath Avatar Cropper
 * Interactive, high-performance, touch & mouse enabled avatar crop modal.
 */
(function(window, document) {
  'use strict';

  class SkillPathAvatarCropper {
    constructor() {
      this.modalEl = null;
      this.canvas = null;
      this.ctx = null;
      this.previewCanvas = null;
      this.previewCtx = null;
      this.img = new Image();
      this.imgLoaded = false;

      // State
      this.currentFile = null;
      this.targetInput = null;
      this.previewTargets = [];
      this.onCropCallback = null;
      this.onCancelCallback = null;

      // Transform state
      this.zoom = 1;
      this.minZoom = 0.5;
      this.maxZoom = 3.5;
      this.baseScale = 1;
      this.rotation = 0; // 0, 90, 180, 270
      this.panX = 0;
      this.panY = 0;

      // Drag state
      this.isDragging = false;
      this.dragStartX = 0;
      this.dragStartY = 0;
      this.initialPanX = 0;
      this.initialPanY = 0;

      // Touch pinch state
      this.initialPinchDistance = 0;
      this.initialPinchZoom = 1;

      // Dimensions
      this.viewportSize = 340;
      this.cropRadius = 140; // 280px diameter circle

      this.initModal();
    }

    initModal() {
      if (document.getElementById('spAvatarCropModal')) {
        this.modalEl = document.getElementById('spAvatarCropModal');
        this.canvas = document.getElementById('spCropCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.previewCanvas = document.getElementById('spCropLivePreviewCanvas');
        this.previewCtx = this.previewCanvas ? this.previewCanvas.getContext('2d') : null;
        this.bindEvents();
        return;
      }

      const modalHtml = `
        <div id="spAvatarCropModal" class="sp-crop-backdrop" aria-hidden="true" role="dialog" aria-modal="true">
          <div class="sp-crop-dialog">
            <div class="sp-crop-header">
              <div class="sp-crop-header-text">
                <div class="sp-crop-badge">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83M16.62 12l-5.74 9.94"></path>
                  </svg>
                  <span>Frame Avatar</span>
                </div>
                <h3>Sesuaikan Foto Profil</h3>
                <p>Geser dan atur ukuran foto agar pas di dalam lingkaran frame profil.</p>
              </div>
              <button type="button" class="sp-crop-close" id="spCropCloseBtn" aria-label="Tutup">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <div class="sp-crop-body">
              <div class="sp-crop-canvas-area">
                <div class="sp-crop-viewport-wrap">
                  <canvas id="spCropCanvas" width="340" height="340" class="sp-crop-canvas"></canvas>
                  <div class="sp-crop-guide-hint">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M5 9l-3 3 3 3M9 5l3-3 3 3M15 19l-3 3-3-3M19 9l3 3-3 3M2 12h20M12 2v20"></path>
                    </svg>
                    <span>Seret untuk menggeser &bull; Scroll untuk zoom</span>
                  </div>
                </div>
              </div>

              <div class="sp-crop-sidebar">
                <!-- Live Circular Preview -->
                <div class="sp-crop-preview-card">
                  <span class="sp-crop-section-label">Pratinjau Frame</span>
                  <div class="sp-crop-preview-avatar-box">
                    <div class="sp-crop-preview-ring">
                      <canvas id="spCropLivePreviewCanvas" width="90" height="90" class="sp-crop-preview-canvas"></canvas>
                    </div>
                  </div>
                  <span class="sp-crop-preview-sub">Tampilan di dashboard & header</span>
                </div>

                <!-- Zoom Controls -->
                <div class="sp-crop-control-group">
                  <div class="sp-crop-control-label-row">
                    <span class="sp-crop-control-label">Perbesar / Perkecil</span>
                    <span id="spCropZoomLabel" class="sp-crop-val-label">100%</span>
                  </div>
                  <div class="sp-crop-slider-row">
                    <button type="button" class="sp-crop-btn-icon" id="spCropZoomOutBtn" title="Perkecil (-)">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                    <input type="range" id="spCropZoomSlider" min="0.5" max="3.0" step="0.01" value="1.0" class="sp-crop-slider">
                    <button type="button" class="sp-crop-btn-icon" id="spCropZoomInBtn" title="Perbesar (+)">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                  </div>
                </div>

                <!-- Rotation & Reset Controls -->
                <div class="sp-crop-control-group">
                  <span class="sp-crop-control-label">Putar & Posisi</span>
                  <div class="sp-crop-btn-group">
                    <button type="button" class="sp-crop-btn-tool" id="spCropRotateLeftBtn" title="Putar Kiri 90°">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                      <span>-90°</span>
                    </button>
                    <button type="button" class="sp-crop-btn-tool" id="spCropRotateRightBtn" title="Putar Kanan 90°">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                      <span>+90°</span>
                    </button>
                    <button type="button" class="sp-crop-btn-tool" id="spCropResetBtn" title="Reset ke Posisi Awal">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                      <span>Reset</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="sp-crop-footer">
              <div class="sp-crop-footer-hint">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Foto akan otomatis dipotong sesuai lingkaran frame profil</span>
              </div>
              <div class="sp-crop-actions">
                <button type="button" class="sp-crop-btn-cancel" id="spCropCancelBtn">Batal</button>
                <button type="button" class="sp-crop-btn-apply" id="spCropApplyBtn">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                  <span>Terapkan Foto</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      `;

      document.body.insertAdjacentHTML('beforeend', modalHtml);
      this.modalEl = document.getElementById('spAvatarCropModal');
      this.canvas = document.getElementById('spCropCanvas');
      this.ctx = this.canvas.getContext('2d');
      this.previewCanvas = document.getElementById('spCropLivePreviewCanvas');
      this.previewCtx = this.previewCanvas ? this.previewCanvas.getContext('2d') : null;

      this.bindEvents();
    }

    bindEvents() {
      // Close & cancel
      document.getElementById('spCropCloseBtn')?.addEventListener('click', () => this.cancel());
      document.getElementById('spCropCancelBtn')?.addEventListener('click', () => this.cancel());
      this.modalEl?.addEventListener('click', (e) => {
        if (e.target === this.modalEl) this.cancel();
      });

      // Apply
      document.getElementById('spCropApplyBtn')?.addEventListener('click', () => this.applyCrop());

      // Slider & buttons
      const slider = document.getElementById('spCropZoomSlider');
      slider?.addEventListener('input', (e) => {
        this.setZoom(parseFloat(e.target.value));
      });

      document.getElementById('spCropZoomInBtn')?.addEventListener('click', () => {
        this.setZoom(this.zoom + 0.15);
      });

      document.getElementById('spCropZoomOutBtn')?.addEventListener('click', () => {
        this.setZoom(this.zoom - 0.15);
      });

      document.getElementById('spCropRotateLeftBtn')?.addEventListener('click', () => {
        this.rotate(-90);
      });

      document.getElementById('spCropRotateRightBtn')?.addEventListener('click', () => {
        this.rotate(90);
      });

      document.getElementById('spCropResetBtn')?.addEventListener('click', () => {
        this.resetTransform();
      });

      // Canvas Dragging & Wheel (Mouse)
      this.canvas.addEventListener('mousedown', (e) => this.onMouseDown(e));
      window.addEventListener('mousemove', (e) => this.onMouseMove(e));
      window.addEventListener('mouseup', () => this.onMouseUp());
      this.canvas.addEventListener('wheel', (e) => this.onWheel(e), { passive: false });

      // Canvas Dragging & Pinch (Touch)
      this.canvas.addEventListener('touchstart', (e) => this.onTouchStart(e), { passive: false });
      this.canvas.addEventListener('touchmove', (e) => this.onTouchMove(e), { passive: false });
      this.canvas.addEventListener('touchend', (e) => this.onTouchEnd(e));

      // Keydown escape
      window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && this.modalEl.classList.contains('active')) {
          this.cancel();
        }
      });
    }

    onMouseDown(e) {
      if (!this.imgLoaded) return;
      e.preventDefault();
      this.isDragging = true;
      this.dragStartX = e.clientX;
      this.dragStartY = e.clientY;
      this.initialPanX = this.panX;
      this.initialPanY = this.panY;
      this.canvas.style.cursor = 'grabbing';
    }

    onMouseMove(e) {
      if (!this.isDragging || !this.imgLoaded) return;
      const dx = e.clientX - this.dragStartX;
      const dy = e.clientY - this.dragStartY;
      this.panX = this.initialPanX + dx;
      this.panY = this.initialPanY + dy;
      this.draw();
    }

    onMouseUp() {
      if (this.isDragging) {
        this.isDragging = false;
        this.canvas.style.cursor = 'grab';
      }
    }

    onWheel(e) {
      if (!this.imgLoaded) return;
      e.preventDefault();
      const zoomFactor = e.deltaY < 0 ? 0.08 : -0.08;
      this.setZoom(this.zoom + zoomFactor);
    }

    onTouchStart(e) {
      if (!this.imgLoaded) return;
      if (e.touches.length === 1) {
        e.preventDefault();
        this.isDragging = true;
        this.dragStartX = e.touches[0].clientX;
        this.dragStartY = e.touches[0].clientY;
        this.initialPanX = this.panX;
        this.initialPanY = this.panY;
      } else if (e.touches.length === 2) {
        e.preventDefault();
        this.isDragging = false;
        this.initialPinchDistance = this.getTouchDistance(e.touches[0], e.touches[1]);
        this.initialPinchZoom = this.zoom;
      }
    }

    onTouchMove(e) {
      if (!this.imgLoaded) return;
      if (e.touches.length === 1 && this.isDragging) {
        e.preventDefault();
        const dx = e.touches[0].clientX - this.dragStartX;
        const dy = e.touches[0].clientY - this.dragStartY;
        this.panX = this.initialPanX + dx;
        this.panY = this.initialPanY + dy;
        this.draw();
      } else if (e.touches.length === 2) {
        e.preventDefault();
        const currentDist = this.getTouchDistance(e.touches[0], e.touches[1]);
        if (this.initialPinchDistance > 0) {
          const factor = currentDist / this.initialPinchDistance;
          this.setZoom(this.initialPinchZoom * factor);
        }
      }
    }

    onTouchEnd(e) {
      if (e.touches.length === 0) {
        this.isDragging = false;
      }
    }

    getTouchDistance(t1, t2) {
      const dx = t1.clientX - t2.clientX;
      const dy = t1.clientY - t2.clientY;
      return Math.sqrt(dx * dx + dy * dy);
    }

    setZoom(val) {
      this.zoom = Math.max(this.minZoom, Math.min(this.maxZoom, val));
      const slider = document.getElementById('spCropZoomSlider');
      if (slider) slider.value = this.zoom.toFixed(2);
      const label = document.getElementById('spCropZoomLabel');
      if (label) label.textContent = Math.round(this.zoom * 100) + '%';
      this.draw();
    }

    rotate(angleDelta) {
      this.rotation = (this.rotation + angleDelta + 360) % 360;
      this.draw();
    }

    resetTransform() {
      this.rotation = 0;
      this.panX = 0;
      this.panY = 0;

      // Compute base scale to cover circle diameter (280px)
      const circleDiameter = this.cropRadius * 2;
      const scaleX = circleDiameter / this.img.naturalWidth;
      const scaleY = circleDiameter / this.img.naturalHeight;
      this.baseScale = Math.max(scaleX, scaleY);
      this.zoom = 1;

      this.setZoom(1);
    }

    open(file, options = {}) {
      if (!file || !file.type.startsWith('image/')) {
        alert('Silakan pilih file gambar yang valid (JPG, PNG, atau WEBP).');
        return;
      }

      this.currentFile = file;
      this.targetInput = options.targetInput || null;
      this.previewTargets = options.previewTargets || [];
      this.onCropCallback = options.onCrop || null;
      this.onCancelCallback = options.onCancel || null;

      const reader = new FileReader();
      reader.onload = (e) => {
        this.img = new Image();
        this.img.onload = () => {
          this.imgLoaded = true;
          this.resetTransform();
          this.showModal();
          this.draw();
        };
        this.img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }

    showModal() {
      this.modalEl.classList.add('active');
      this.modalEl.setAttribute('aria-hidden', 'false');
      document.body.classList.add('sp-crop-modal-open');
    }

    hideModal() {
      this.modalEl.classList.remove('active');
      this.modalEl.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('sp-crop-modal-open');
    }

    cancel() {
      this.hideModal();
      if (this.targetInput && !this.targetInput._hasCroppedValue) {
        this.targetInput.value = '';
      }
      if (typeof this.onCancelCallback === 'function') {
        this.onCancelCallback();
      }
    }

    draw() {
      if (!this.imgLoaded) return;

      const ctx = this.ctx;
      const w = this.canvas.width;
      const h = this.canvas.height;
      const centerX = w / 2;
      const centerY = h / 2;

      ctx.clearRect(0, 0, w, h);

      // 1. Draw checkered transparent pattern
      this.drawCheckered(ctx, w, h);

      // 2. Draw transformed image
      ctx.save();
      ctx.translate(centerX + this.panX, centerY + this.panY);
      ctx.rotate((this.rotation * Math.PI) / 180);
      const currentScale = this.baseScale * this.zoom;
      const drawW = this.img.naturalWidth * currentScale;
      const drawH = this.img.naturalHeight * currentScale;
      ctx.drawImage(this.img, -drawW / 2, -drawH / 2, drawW, drawH);
      ctx.restore();

      // 3. Draw dark overlay mask outside crop circle
      ctx.save();
      ctx.fillStyle = 'rgba(15, 23, 42, 0.68)';
      ctx.beginPath();
      // Outer rect
      ctx.rect(0, 0, w, h);
      // Inner circle cutout (anticlockwise)
      ctx.arc(centerX, centerY, this.cropRadius, 0, Math.PI * 2, true);
      ctx.fill();
      ctx.restore();

      // 4. Draw avatar circular border & rule of thirds grid inside circle
      ctx.save();
      ctx.beginPath();
      ctx.arc(centerX, centerY, this.cropRadius, 0, Math.PI * 2);
      ctx.clip();

      // Rule of thirds lines
      ctx.strokeStyle = 'rgba(255, 255, 255, 0.28)';
      ctx.lineWidth = 1;
      const r = this.cropRadius;
      const third = (r * 2) / 3;

      ctx.beginPath();
      // Vertical grid lines
      ctx.moveTo(centerX - r + third, centerY - r);
      ctx.lineTo(centerX - r + third, centerY + r);
      ctx.moveTo(centerX - r + third * 2, centerY - r);
      ctx.lineTo(centerX - r + third * 2, centerY + r);
      // Horizontal grid lines
      ctx.moveTo(centerX - r, centerY - r + third);
      ctx.lineTo(centerX + r, centerY - r + third);
      ctx.moveTo(centerX - r, centerY - r + third * 2);
      ctx.lineTo(centerX + r, centerY - r + third * 2);
      ctx.stroke();

      ctx.restore();

      // Circle boundary ring
      ctx.save();
      ctx.beginPath();
      ctx.arc(centerX, centerY, this.cropRadius, 0, Math.PI * 2);
      ctx.strokeStyle = '#6857df';
      ctx.lineWidth = 2.5;
      ctx.stroke();

      // Outer glow line
      ctx.beginPath();
      ctx.arc(centerX, centerY, this.cropRadius + 1, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(255, 255, 255, 0.75)';
      ctx.lineWidth = 1;
      ctx.stroke();
      ctx.restore();

      // 5. Draw live mini preview
      this.drawLivePreview();
    }

    drawCheckered(ctx, w, h) {
      const size = 12;
      ctx.fillStyle = '#f1f5f9';
      ctx.fillRect(0, 0, w, h);
      ctx.fillStyle = '#e2e8f0';
      for (let y = 0; y < h; y += size) {
        for (let x = 0; x < w; x += size) {
          if ((Math.floor(x / size) + Math.floor(y / size)) % 2 === 0) {
            ctx.fillRect(x, y, size, size);
          }
        }
      }
    }

    drawLivePreview() {
      if (!this.previewCtx || !this.imgLoaded) return;

      const pCtx = this.previewCtx;
      const pw = this.previewCanvas.width;
      const ph = this.previewCanvas.height;
      const pcX = pw / 2;
      const pcY = ph / 2;
      const pRadius = pw / 2;

      pCtx.clearRect(0, 0, pw, ph);

      // Clip to circle
      pCtx.save();
      pCtx.beginPath();
      pCtx.arc(pcX, pcY, pRadius, 0, Math.PI * 2);
      pCtx.clip();

      // Checkered background
      pCtx.fillStyle = '#ffffff';
      pCtx.fillRect(0, 0, pw, ph);

      // Draw transformed image scaled down to preview canvas
      const ratio = pRadius / this.cropRadius;
      pCtx.translate(pcX + this.panX * ratio, pcY + this.panY * ratio);
      pCtx.rotate((this.rotation * Math.PI) / 180);
      const currentScale = this.baseScale * this.zoom * ratio;
      const drawW = this.img.naturalWidth * currentScale;
      const drawH = this.img.naturalHeight * currentScale;
      pCtx.drawImage(this.img, -drawW / 2, -drawH / 2, drawW, drawH);

      pCtx.restore();
    }

    applyCrop() {
      if (!this.imgLoaded) return;

      // Render high-res cropped output (512 x 512)
      const outputSize = 512;
      const outCanvas = document.createElement('canvas');
      outCanvas.width = outputSize;
      outCanvas.height = outputSize;
      const outCtx = outCanvas.getContext('2d');

      const outCenterX = outputSize / 2;
      const outCenterY = outputSize / 2;
      const exportScaleFactor = (outputSize / 2) / this.cropRadius;

      // Optional: fill white background for JPG compatibility
      outCtx.fillStyle = '#ffffff';
      outCtx.fillRect(0, 0, outputSize, outputSize);

      outCtx.save();
      outCtx.translate(outCenterX + this.panX * exportScaleFactor, outCenterY + this.panY * exportScaleFactor);
      outCtx.rotate((this.rotation * Math.PI) / 180);

      const currentScale = this.baseScale * this.zoom * exportScaleFactor;
      const drawW = this.img.naturalWidth * currentScale;
      const drawH = this.img.naturalHeight * currentScale;
      outCtx.drawImage(this.img, -drawW / 2, -drawH / 2, drawW, drawH);
      outCtx.restore();

      // Convert to Blob & File
      outCanvas.toBlob((blob) => {
        if (!blob) {
          alert('Terjadi kesalahan saat memproses gambar.');
          return;
        }

        const originalName = this.currentFile ? this.currentFile.name.replace(/\.[^/.]+$/, '') : 'avatar';
        const croppedFile = new File([blob], `${originalName}_cropped.jpg`, {
          type: 'image/jpeg',
          lastModified: Date.now()
        });

        // Inject cropped file into target input if available
        if (this.targetInput) {
          try {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            this.targetInput.files = dataTransfer.files;
            this.targetInput._hasCroppedValue = true;
          } catch (err) {
            console.warn('DataTransfer not supported, falling back to callback', err);
          }
        }

        // Update preview targets on page
        const previewUrl = URL.createObjectURL(blob);
        this.previewTargets.forEach((target) => {
          if (typeof target === 'string') {
            const el = document.querySelector(target);
            if (el) this.updatePreviewElement(el, previewUrl);
          } else if (target && target.nodeType) {
            this.updatePreviewElement(target, previewUrl);
          }
        });

        if (typeof this.onCropCallback === 'function') {
          this.onCropCallback({ blob, file: croppedFile, dataUrl: outCanvas.toDataURL('image/jpeg', 0.92) });
        }

        this.hideModal();
      }, 'image/jpeg', 0.92);
    }

    updatePreviewElement(el, previewUrl) {
      if (el.tagName === 'IMG') {
        el.src = previewUrl;
        el.style.display = 'block';
      } else {
        const img = el.querySelector('img');
        const initial = el.querySelector('span');
        if (img) {
          img.src = previewUrl;
          img.style.display = 'block';
        }
        if (initial) {
          initial.style.display = 'none';
        }
      }

      // Add a subtle celebratory pulse animation
      el.classList.remove('sp-avatar-pulse');
      void el.offsetWidth; // trigger reflow
      el.classList.add('sp-avatar-pulse');
    }

    /**
     * Helper to bind any file input with auto-cropping
     */
    static bind(inputSelectorOrEl, options = {}) {
      const input = typeof inputSelectorOrEl === 'string' 
        ? document.querySelector(inputSelectorOrEl) 
        : inputSelectorOrEl;

      if (!input) return;

      const instance = window.SkillPathCropperInstance || (window.SkillPathCropperInstance = new SkillPathAvatarCropper());

      input.addEventListener('change', function(e) {
        if (input.files && input.files[0]) {
          const file = input.files[0];
          // If this file was programmatically set by our cropper, don't re-open modal
          if (file.name.endsWith('_cropped.jpg') && input._hasCroppedValue) {
            return;
          }
          input._hasCroppedValue = false;
          instance.open(file, {
            targetInput: input,
            previewTargets: options.previewTargets || [],
            onCrop: options.onCrop,
            onCancel: options.onCancel
          });
        }
      });

      return instance;
    }
  }

  // Expose globally
  window.SkillPathAvatarCropper = SkillPathAvatarCropper;

  // Auto-bind on DOM ready for inputs with data-crop-avatar
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[type="file"][data-crop-avatar]').forEach((input) => {
      const previewSelector = input.getAttribute('data-preview-target');
      SkillPathAvatarCropper.bind(input, {
        previewTargets: previewSelector ? [previewSelector] : []
      });
    });
  });

})(window, document);
