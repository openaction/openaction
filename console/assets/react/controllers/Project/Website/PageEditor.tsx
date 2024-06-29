import React from 'react';
import {WebEditor} from "../../../components/WebEditor/WebEditor";

interface Props {
  content: string;
}

export default function PageEditor(props: Props) {
  return (
    <div className="p-4">
      <WebEditor />
    </div>
  );
}
