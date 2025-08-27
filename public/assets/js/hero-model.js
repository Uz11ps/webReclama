/* global THREE, gsap */
// Дополнительная загрузка GLTF/DRACO модели для хиро. Файл модели можно позже заменить.
// Лёгкая абстракция: если нет THREE или WebGL2 — тихо выходим.

if (window.THREE) {
  (async function initHeroModel() {
    try {
      const supportsWebGL = (() => {
        try {
          const canvas = document.createElement('canvas');
          return !!(window.WebGLRenderingContext && (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
        } catch (e) { return false; }
      })();
      if (!supportsWebGL) return;

      // Lazy import GLTFLoader & DRACOLoader via CDN
      const [{ GLTFLoader }, { DRACOLoader }] = await Promise.all([
        import('https://cdn.jsdelivr.net/npm/three@0.152/examples/jsm/loaders/GLTFLoader.js'),
        import('https://cdn.jsdelivr.net/npm/three@0.152/examples/jsm/loaders/DRACOLoader.js')
      ]);

      // Reuse scene from base app if available
      const canvases = document.querySelectorAll('canvas.webgl');
      if (!canvases.length) return;
      const canvas = canvases[0];
      // Attach to existing renderer/scene is non-trivial; создадим вторую сцену поверх как отдельный слой
      const container = document.getElementById('webgl-hero');
      const scene = new THREE.Scene();
      const camera = new THREE.PerspectiveCamera(55, window.innerWidth / window.innerHeight, 0.1, 1000);
      camera.position.set(0, 0.2, 2.6);

      const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
      renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
      renderer.setSize(window.innerWidth, window.innerHeight);
      renderer.domElement.style.pointerEvents = 'none';
      renderer.domElement.classList.add('webgl');
      container.appendChild(renderer.domElement);

      const ambient = new THREE.AmbientLight(0xffffff, 0.6);
      const dir = new THREE.DirectionalLight(0xffffff, 0.8);
      dir.position.set(2, 3, 4);
      scene.add(ambient, dir);

      const draco = new DRACOLoader();
      draco.setDecoderPath('https://cdn.jsdelivr.net/npm/three@0.152/examples/jsm/libs/draco/');
      const loader = new GLTFLoader();
      loader.setDRACOLoader(draco);

      // Placeholder модель — минимальная, замените на свою: /assets/models/hero.glb
      const modelUrl = 'assets/models/hero.glb';
      let root;
      try {
        const gltf = await loader.loadAsync(modelUrl);
        root = gltf.scene;
      } catch (e) {
        // Нет модели — создадим премиум-ось из примитивов
        const group = new THREE.Group();
        const mat = new THREE.MeshStandardMaterial({ color: 0xffffff, metalness: 0.8, roughness: 0.2 });
        const geo1 = new THREE.TorusKnotGeometry(0.5, 0.16, 200, 32);
        const mesh1 = new THREE.Mesh(geo1, mat);
        group.add(mesh1);
        root = group;
      }

      root.position.set(0.8, -0.2, 0);
      scene.add(root);

      const onResize = () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
      };
      window.addEventListener('resize', onResize);

      let t = 0;
      const animate = () => {
        t += 0.016;
        if (root) {
          root.rotation.y = t * 0.2;
          root.rotation.x = Math.sin(t * 0.3) * 0.1;
        }
        renderer.render(scene, camera);
        requestAnimationFrame(animate);
      };
      animate();
    } catch (e) {
      // тихо игнорируем, это необязательный слой
    }
  })();
}

