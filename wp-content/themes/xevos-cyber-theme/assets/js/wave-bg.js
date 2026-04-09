/**
 * Wave background – Three.js particle grid.
 * No EffectComposer/bloom – glow baked into shader for true alpha transparency.
 */
import * as THREE from 'three';

export function initWaveBg(container) {
  if (!container) return;

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(45, container.offsetWidth / container.offsetHeight, 0.1, 100);
  camera.position.set(0, 7.5, 16);
  camera.lookAt(0, 0, 0);

  const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
  renderer.setClearColor(0x000000, 0);
  renderer.setSize(container.offsetWidth, container.offsetHeight);
  renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
  container.appendChild(renderer.domElement);

  const w = 420, d = 220, count = w * d;
  const positions = new Float32Array(count * 3);
  const uvs = new Float32Array(count * 2);
  let i3 = 0, i2 = 0;

  for (let z = 0; z < d; z++) {
    for (let x = 0; x < w; x++) {
      positions[i3]     = (x / (w - 1) - 0.5) * 34;
      positions[i3 + 1] = 0;
      positions[i3 + 2] = (z / (d - 1) - 0.5) * 18;
      uvs[i2]     = x / (w - 1);
      uvs[i2 + 1] = z / (d - 1);
      i3 += 3;
      i2 += 2;
    }
  }

  const geo = new THREE.BufferGeometry();
  geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  geo.setAttribute('uv', new THREE.BufferAttribute(uvs, 2));

  const mat = new THREE.ShaderMaterial({
    transparent: true,
    depthWrite: false,
    blending: THREE.AdditiveBlending,
    uniforms: {
      uTime: { value: 0 },
      uPointSize: { value: 4.5 },
      uColorA: { value: new THREE.Color('#2f6bff') },
      uColorB: { value: new THREE.Color('#6a4cff') },
      uColorC: { value: new THREE.Color('#d96cff') }
    },
    vertexShader: `
      uniform float uTime;
      uniform float uPointSize;
      varying float vH;
      varying vec2 vUv;
      void main(){
        vUv = uv;
        vec3 pos = position;
        float wave =
          sin(pos.x*0.6 + uTime*0.6)*0.8 +
          sin(pos.z*1.2 - uTime*0.7)*0.3 +
          sin((pos.x+pos.z)*0.4 - uTime*0.4)*0.4;
        float ridge = exp(-pow(pos.x*0.12,2.0))*1.2;
        pos.y = (wave + ridge) * 1.8;
        vec4 mv = modelViewMatrix * vec4(pos,1.0);
        gl_Position = projectionMatrix * mv;
        gl_PointSize = uPointSize * (18.0 / -mv.z);
        vH = pos.y;
      }
    `,
    fragmentShader: `
      uniform vec3 uColorA;
      uniform vec3 uColorB;
      uniform vec3 uColorC;
      varying float vH;
      varying vec2 vUv;
      void main(){
        vec2 c = gl_PointCoord - 0.5;
        float dist = length(c);

        // Bright core + soft glow (replaces bloom)
        float core = 1.0 - smoothstep(0.0, 0.12, dist);
        float glow = 1.0 - smoothstep(0.0, 0.5, dist);

        float h = clamp(vH*0.15+0.5, 0.0, 1.0);
        vec3 col = mix(uColorA, uColorB, h);
        col = mix(col, uColorC, pow(h, 1.8));

        // Brighter core to simulate bloom
        col += core * 0.4;

        float fade = smoothstep(1.0, 0.2, vUv.y);
        float alpha = core * 1.0 + glow * 0.35;

        gl_FragColor = vec4(col, alpha * fade);
      }
    `
  });

  const points = new THREE.Points(geo, mat);
  points.rotation.x = -0.55;
  scene.add(points);

  const clock = new THREE.Clock();

  function animate() {
    requestAnimationFrame(animate);
    mat.uniforms.uTime.value = clock.getElapsedTime();
    renderer.render(scene, camera);
  }
  animate();

  const ro = new ResizeObserver(() => {
    const w = container.offsetWidth;
    const h = container.offsetHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
  });
  ro.observe(container);

  return function dispose() {
    ro.disconnect();
    renderer.dispose();
    geo.dispose();
    mat.dispose();
  };
}
