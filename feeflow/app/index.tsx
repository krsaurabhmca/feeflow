import { Redirect } from 'expo-router';
import { useEffect, useState } from 'react';
import * as SecureStore from 'expo-secure-store';

export default function Index() {
  const [target, setTarget] = useState<string | null>(null);

  useEffect(() => {
    SecureStore.getItemAsync('api_key').then(key => {
      setTarget(key ? '/(tabs)' : '/login');
    });
  }, []);

  if (!target) return null;

  return <Redirect href={target as any} />;
}
