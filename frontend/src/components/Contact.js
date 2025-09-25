import React from 'react';

const Contact = () => {
  return (
    <section id="contact" className="py-20 bg-gray-800 text-white">
      <div className="container mx-auto px-6">
        <h2 className="text-4xl font-bold text-center mb-12">Contact Me</h2>
        <div className="max-w-lg mx-auto">
          <form action="mailto:your-email@example.com" method="POST" encType="text/plain">
            <div className="mb-4">
              <label htmlFor="name" className="block mb-2">Name</label>
              <input type="text" id="name" name="name" className="w-full p-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:border-blue-500" required />
            </div>
            <div className="mb-4">
              <label htmlFor="email" className="block mb-2">Email</label>
              <input type="email" id="email" name="email" className="w-full p-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:border-blue-500" required />
            </div>
            <div className="mb-4">
              <label htmlFor="message" className="block mb-2">Message</label>
              <textarea id="message" name="message" rows="4" className="w-full p-2 rounded bg-gray-700 border border-gray-600 focus:outline-none focus:border-blue-500" required></textarea>
            </div>
            <button type="submit" className="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
              Send Message
            </button>
          </form>
          <div className="text-center mt-8">
            <p>Or connect with me on social media:</p>
            <div className="flex justify-center space-x-4 mt-4">
              <a href="#" className="text-gray-400 hover:text-white">Twitter</a>
              <a href="#" className="text-gray-400 hover:text-white">LinkedIn</a>
              <a href="#" className="text-gray-400 hover:text-white">GitHub</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Contact;