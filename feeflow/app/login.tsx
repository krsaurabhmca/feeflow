import React, { useState } from 'react';
import { StyleSheet, View, Text, TextInput, TouchableOpacity, Image, KeyboardAvoidingView, Platform, Alert, ActivityIndicator } from 'react-native';
import { useRouter } from 'expo-router';
import * as SecureStore from 'expo-secure-store';
import { login } from '../lib/api';
import { StatusBar } from 'expo-status-bar';

export default function LoginScreen() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const router = useRouter();

    const handleLogin = async () => {
        if (!email || !password) {
            Alert.alert('Error', 'Please fill in all fields');
            return;
        }

        setLoading(true);
        try {
            const response = await login(email, password);
            if (response.status) {
                await SecureStore.setItemAsync('api_key', response.data.api_key);
                await SecureStore.setItemAsync('institute_name', response.data.name);
                router.replace('/(tabs)');
            } else {
                Alert.alert('Error', response.message);
            }
        } catch (error) {
            Alert.alert('Error', 'Failed to connect to server. Check your network.');
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <KeyboardAvoidingView
            behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
            style={styles.container}
        >
            <StatusBar style="dark" />
            <View style={styles.card}>
                <View style={styles.header}>
                    <Text style={styles.logoText}>FeeFlow</Text>
                    <Text style={styles.subText}>Manage your institute on the go</Text>
                </View>

                <View style={styles.form}>
                    <Text style={styles.label}>Email Address</Text>
                    <TextInput
                        style={styles.input}
                        placeholder="admin@example.com"
                        value={email}
                        onChangeText={setEmail}
                        keyboardType="email-address"
                        autoCapitalize="none"
                    />

                    <Text style={styles.label}>Password</Text>
                    <TextInput
                        style={styles.input}
                        placeholder="••••••••"
                        value={password}
                        onChangeText={setPassword}
                        secureTextEntry
                    />

                    <TouchableOpacity
                        style={styles.button}
                        onPress={handleLogin}
                        disabled={loading}
                    >
                        {loading ? (
                            <ActivityIndicator color="white" />
                        ) : (
                            <Text style={styles.buttonText}>Login</Text>
                        )}
                    </TouchableOpacity>
                </View>
            </View>
        </KeyboardAvoidingView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#f8fafc',
        justifyContent: 'center',
        padding: 20,
    },
    card: {
        backgroundColor: 'white',
        borderRadius: 20,
        padding: 30,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 10 },
        shadowOpacity: 0.1,
        shadowRadius: 20,
        elevation: 5,
    },
    header: {
        alignItems: 'center',
        marginBottom: 30,
    },
    logoText: {
        fontSize: 32,
        fontWeight: '900',
        color: '#dc2626',
    },
    subText: {
        color: '#64748b',
        marginTop: 5,
    },
    form: {
        gap: 15,
    },
    label: {
        fontWeight: '600',
        color: '#1e293b',
        marginBottom: -10,
        fontSize: 14,
    },
    input: {
        borderWidth: 1,
        borderColor: '#e2e8f0',
        borderRadius: 12,
        padding: 15,
        fontSize: 16,
        backgroundColor: '#f1f5f9',
    },
    button: {
        backgroundColor: '#dc2626',
        borderRadius: 12,
        padding: 18,
        alignItems: 'center',
        marginTop: 10,
    },
    buttonText: {
        color: 'white',
        fontWeight: '800',
        fontSize: 16,
    },
});
