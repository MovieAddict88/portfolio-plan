import React, { useState, useEffect } from 'react';
import Home from './components/Home';
import About from './components/About';
import Experience from './components/Experience';
import Skills from './components/Skills';
import Projects from './components/Projects';
import DownloadCenter from './components/DownloadCenter';
import Contact from './components/Contact';

function App() {
  const [data, setData] = useState({ about_me: {}, skills: [], experience: [], projects: [], documents: [] });

  useEffect(() => {
    fetch('/api/portfolio-data.php') // This is our new API endpoint
      .then(res => {
        if (!res.ok) {
          throw new Error('Network response was not ok');
        }
        return res.json();
      })
      .then(fetchedData => {
        setData(fetchedData);
      })
      .catch(err => {
        console.error("Failed to fetch data:", err);
        // Optionally, set some error state to show in the UI
      });
  }, []);

  return (
    <div className="bg-gray-100 min-h-screen">
      <header className="bg-white shadow fixed w-full z-50">
        <nav className="container mx-auto px-6 py-3">
          <div className="flex justify-between items-center">
            <div className="text-xl font-semibold text-gray-700">{data.about_me.name}'s Portfolio</div>
            <div>
              <a href="#home" className="px-3 py-2 text-gray-700 hover:text-gray-900">Home</a>
              <a href="#about" className="px-3 py-2 text-gray-700 hover:text-gray-900">About</a>
              <a href="#experience" className="px-3 py-2 text-gray-700 hover:text-gray-900">Experience</a>
              <a href="#skills" className="px-3 py-2 text-gray-700 hover:text-gray-900">Skills</a>
              <a href="#projects" className="px-3 py-2 text-gray-700 hover:text-gray-900">Projects</a>
              <a href="#download" className="px-3 py-2 text-gray-700 hover:text-gray-900">Download</a>
              <a href="#contact" className="px-3 py-2 text-gray-700 hover:text-gray-900">Contact</a>
            </div>
          </div>
        </nav>
      </header>

      <main>
        <Home name={data.about_me.name} tagline="Dedicated to Inspiring the Next Generation" photo_url={data.about_me.photo_url} />
        <About {...data.about_me} />
        <Experience experiences={data.experience} />
        <Skills skills={data.skills} />
        <Projects projects={data.projects} />
        <DownloadCenter documents={data.documents} />
        <Contact />
      </main>
    </div>
  );
}

export default App;