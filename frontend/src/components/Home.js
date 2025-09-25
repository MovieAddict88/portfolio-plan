import React from 'react';

const Home = ({ name, tagline, photo_url }) => {
  const backgroundImageUrl = photo_url || 'https://via.placeholder.com/1920x1080';
  return (
    <section id="home" className="h-screen flex items-center justify-center bg-cover bg-center" style={{backgroundImage: `url('${backgroundImageUrl}')`}}>
      <div className="text-center text-white bg-black bg-opacity-50 p-10 rounded-lg">
        <h1 className="text-5xl font-bold animate-fade-in-down">{name}</h1>
        <p className="text-xl mt-4 animate-fade-in-up">{tagline}</p>
      </div>
    </section>
  );
};

export default Home;