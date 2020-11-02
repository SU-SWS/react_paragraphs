import React from 'react';
import CircularProgress from '@material-ui/core/CircularProgress';

export const Loader = () => {
  return (
    <div style={{position: 'absolute', left: '50%', top: '50%'}}>
      <CircularProgress/>
    </div>
  )
};
