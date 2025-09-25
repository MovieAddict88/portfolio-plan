import React from 'react';

const About = ({ bio, education, philosophy }) => {
  return (
    <section id="about" className="py-20 bg-white">
      <div className="container mx-auto px-6">
        <h2 className="text-4xl font-bold text-center text-gray-800 mb-8">About Me</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="p-6 bg-gray-100 rounded-lg shadow-md animate-slide-in-left">
            <h3 className="text-2xl font-semibold mb-4">Biography</h3>
            <p>{bio}</p>
          </div>
          <div className="p-6 bg-gray-100 rounded-lg shadow-md animate-slide-in-bottom">
            <h3 className="text-2xl font-semibold mb-4">Education</h3>
            <p>{education}</p>
          </div>
          <div className="p-6 bg-gray-100 rounded-lg shadow-md animate-slide-in-right">
            <h3 className="text-2xl font-semibold mb-4">Teaching Philosophy</h3>
            <p>{philosophy}</p>
          </div>
        </div>
      </div>
    </section>
  );
};

export default About;