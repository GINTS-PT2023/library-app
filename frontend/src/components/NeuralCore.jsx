import React from 'react';
import './NeuralCore.css';

const NeuralCore = () => {
  return (
    <div className="neural-core-container overload">
      {/* Background Matrix Grid */}
      <div className="matrix-grid"></div>

      <div className="neural-core">
        {/* Deep Space Shadow */}
        <div className="core-shadow"></div>

        {/* Hyper Rings */}
        <div className="ring ring-ultra outer"></div>
        <div className="ring ring-ultra middle"></div>
        <div className="ring ring-ultra inner"></div>
        
        {/* Geometric HUD Overlays */}
        <div className="hud-layer hex-hud">
          <svg viewBox="0 0 200 200">
            <polygon points="100,10 180,50 180,150 100,190 20,150 20,50" fill="none" stroke="var(--neon-cyan)" strokeWidth="0.5" strokeDasharray="5,5" />
            <circle cx="100" cy="100" r="90" fill="none" stroke="var(--cyber-purple)" strokeWidth="0.2" strokeDasharray="2,10" />
          </svg>
        </div>

        {/* Data Stream Orbits */}
        {[...Array(5)].map((_, i) => (
          <div key={i} className={`hyper-orbit orbit-level-${i+1}`}>
            <div className="glitch-node"></div>
            <div className="data-tag">0x{Math.floor(Math.random()*1000).toString(16).toUpperCase()}</div>
          </div>
        ))}

        {/* Pulsing Core Singularity */}
        <div className="singularity">
          <div className="singularity-inner"></div>
          <div className="singularity-pulse"></div>
          <div className="singularity-glow"></div>
          {/* Central Binary Readout */}
          <div className="binary-readout">
            <span>1010</span>
            <span>0110</span>
          </div>
        </div>

        {/* Rotating Binary Ring */}
        <div className="text-orbit-complex">
          <svg viewBox="0 0 300 300">
            <path id="complexPath" d="M 150, 150 m -120, 0 a 120,120 0 1,1 240,0 a 120,120 0 1,1 -240,0" fill="none" />
            <text>
              <textPath href="#complexPath" className="complex-path-text">
                OVERLOAD_DETECTION_ACTIVE // SYNCING_NEURAL_NODES // 01010111 // ACCESS_GRANTED // 
              </textPath>
            </text>
          </svg>
        </div>

        {/* Floating Particle Field */}
        <div className="particle-field">
          {[...Array(20)].map((_, i) => (
            <div key={i} className="particle" style={{
              '--p-delay': `${Math.random() * 5}s`,
              '--p-top': `${Math.random() * 100}%`,
              '--p-left': `${Math.random() * 100}%`
            }}></div>
          ))}
        </div>
      </div>
      
      {/* Hyper Stat Readouts */}
      <div className="hyper-stats">
        <div className="stat-box">
          <div className="stat-label">CPU_LOAD</div>
          <div className="stat-value-bar"><div className="fill" style={{width: '85%'}}></div></div>
          <div className="stat-value mono">85%</div>
        </div>
        <div className="stat-box">
          <div className="stat-label">NEURAL_SYNC</div>
          <div className="stat-value-bar"><div className="fill purple" style={{width: '92%'}}></div></div>
          <div className="stat-value mono">92%</div>
        </div>
        <div className="stat-box">
          <div className="stat-label">CORE_VOLTAGE</div>
          <div className="stat-value-bar"><div className="fill amber" style={{width: '45%'}}></div></div>
          <div className="stat-value mono">1.28V</div>
        </div>
      </div>
    </div>
  );
};

export default NeuralCore;
