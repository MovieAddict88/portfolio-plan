import React, { useState } from 'react';

const DownloadCenter = ({ documents }) => {
  const [password, setPassword] = useState('');
  const [selectedDoc, setSelectedDoc] = useState(null);
  const [error, setError] = useState('');

  const handleDownloadClick = (doc) => {
    setSelectedDoc(doc);
  };

  const handlePasswordSubmit = (e) => {
    e.preventDefault();
    // In a real app, you might want to make an API call to verify the password first
    // and get a temporary download link. For this project, we'll directly link to the download script.
    window.location.href = `/download.php?doc_id=${selectedDoc.id}&password=${password}`;
    // We don't get feedback here, so we'll just close the modal.
    // A more robust solution would involve AJAX to verify and handle errors.
    setSelectedDoc(null);
  };

  return (
    <section id="download" className="py-20 bg-white">
      <div className="container mx-auto px-6">
        <h2 className="text-4xl font-bold text-center text-gray-800 mb-12">Download Center</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {documents.map(doc => (
                <div key={doc.id} className="p-6 bg-gray-100 rounded-lg shadow-md text-center">
                    <h3 className="text-xl font-semibold mb-4">{doc.file_name}</h3>
                    <button onClick={() => handleDownloadClick(doc)} className="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                        Download
                    </button>
                </div>
            ))}
        </div>

        {selectedDoc && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div className="bg-white p-8 rounded-lg shadow-xl">
              <h3 className="text-2xl font-semibold mb-4">Enter Password for {selectedDoc.file_name}</h3>
              <form onSubmit={handlePasswordSubmit}>
                <input
                  type="password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full p-2 border border-gray-300 rounded mb-4"
                  placeholder="Password"
                  required
                />
                {error && <p className="text-red-500 mb-4">{error}</p>}
                <div className="flex justify-end">
                    <button type="button" onClick={() => setSelectedDoc(null)} className="text-gray-600 mr-4">Cancel</button>
                    <button type="submit" className="bg-blue-500 text-white px-6 py-2 rounded">Submit</button>
                </div>
              </form>
            </div>
          </div>
        )}
      </div>
    </section>
  );
};

export default DownloadCenter;