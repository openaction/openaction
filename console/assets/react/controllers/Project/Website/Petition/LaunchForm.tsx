import React from 'react';

interface Props {
  fields: {
    title: { name: string, value: string },
    content: { name: string, value: string },
    mainImage: { name: string, value: string },
    _token: { name: string, value: string },
  };
}

export default function LaunchForm(props: Props) {
  console.log(props);

  return <div>Hello</div>;
}
