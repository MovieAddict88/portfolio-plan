import React from 'react';

const Skills = ({ skills }) => {
  return (
    <section id="skills" className="py-20 bg-white">
      <div className="container mx-auto px-6">
        <h2 className="text-4xl font-bold text-center text-gray-800 mb-12">Skills</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
          {skills.map(skill => (
            <div key={skill.id} className="mb-4">
              <div className="flex justify-between mb-1">
                <span className="text-base font-medium text-blue-700">{skill.skill_name}</span>
                <span className="text-sm font-medium text-blue-700">{skill.level}%</span>
              </div>
              <div className="w-full bg-gray-200 rounded-full h-2.5">
                <div className="bg-blue-600 h-2.5 rounded-full" style={{ width: `${skill.level}%` }}></div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Skills;