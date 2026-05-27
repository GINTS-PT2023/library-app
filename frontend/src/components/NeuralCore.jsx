import React from 'react';
import './NeuralCore.css';

const NeuralCore = () => {
  return (
    <div className="neural-core-container">
      <div className="neural-core">
        {/* Outer Rotating Ring */}
        <div className="ring outer-ring"></div>
        
        {/* Middle Hexagon Ring */}
        <div className="ring hex-ring">
          <svg viewBox="0 0 100 100">
            <polygon points="50,1 93,25 93,75 50,99 7,75 7,25" fill="none" stroke="var(--neon-cyan)" strokeWidth="0.5" />
          </svg>
        </div>
        
        {/* Inner Pulsing Core */}
        <div className="core-center">
          <div className="core-pulse"></div>
          <div className="core-inner"></div>
        </div>
        
        {/* Data Orbits */}
        <div className="orbit orbit-1">
          <div className="data-node"></div>
        </div>
        <div className="orbit orbit-2">
          <div className="data-node"></div>
        </div>
        
        {/* Decorative Text Orbits */}
        <div className="text-orbit">
          <svg viewBox="0 0 200 200">
            <path id="circlePath" d="M 100, 100 m -75, 0 a 75,75 0 1,1 150,0 a 75,75 0 1,1 -150,0" fill="none" />
            <text>
              <textPath href="#circlePath" className="orbit-text">
                SYSTEM_STABLE // NEURAL_LINK_ACTIVE // DATA_STREAM_0101 // 
              </textPath>
            </text>
          </svg>
        </div>
      </div>
      
      <div className="core-stats">
        <div className="core-stat-item">
          <span className="label">SYNC_RATE</span>
          <span className="value">98.4%</span>
        </div>
        <div className="core-stat-item">
          <span className="label">CORE_TEMP</span>
          <span className="value">32°C</span>
        </div>
        <div className="core-stat-item">
          <span className="label">UPTIME</span>
          <span className="value">14.2h</span>
        </div>
      </div>
    </div>
  );
};

export default NeuralCore;
