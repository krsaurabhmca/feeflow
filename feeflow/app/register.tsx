import React, { useState } from 'react';
import { StyleSheet, View, Text, TextInput, TouchableOpacity, ScrollView, KeyboardAvoidingView, Platform, Alert, ActivityIndicator } from 'react-native';
import { useRouter } from 'expo-router';
import * as SecureStore from 'expo-secure-store';
import { register } from '../lib/api';
import { StatusBar } from 'expo-status-bar';
import { Ionicons, MaterialIcons } from '@expo/vector-icons';

export default function RegisterScreen() {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const [loading, setLoading] = useState(false);
    const router = useRouter();

    const handleRegister = async () => {
        if (!name || !email || !password) {
            Alert.alert('Error', 'Please fill in all fields');
            return;
        }

        setLoading(true);
        try {
            const response = await register({ name, email, password });
            if (response.status) {
                await SecureStore.setItemAsync('api_key', response.data.api_key);
                await SecureStore.setItemAsync('institute_name', response.data.name);
                Alert.alert('Success', 'Institute Registered Successfully!', [
                    { text: 'Continue', onPress: () => router.replace('/(tabs)') }
                ]);
            } else {
                Alert.alert('Error', response.message);
            }
        } catch (error) {
            Alert.alert('Error', 'Registration failed. Check your network.');
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
            <ScrollView contentContainerStyle={styles.scroll}>
                <View style={styles.card}>
                    <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
                        <Ionicons name="arrow-back" size={24} color="#1e293b" />
                    </TouchableOpacity>

                    <View style={styles.header}>
                        <Text style={styles.title}>Register Institute</Text>
                        <Text style={styles.subText}>Create your FeeFlow account today</Text>
                    </View>

                    <View style={styles.form}>
                        <Text style={styles.label}>Institute Name</Text>
                        <TextInput
                            style={styles.input}
                            placeholder="e.g. Acme International School"
                            value={name}
                            onChangeText={setName}
                        />

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
                        <View style={styles.passwordContainer}>
                            <TextInput
                                style={styles.passwordInput}
                                placeholder="••••••••"
                                value={password}
                                onChangeText={setPassword}
                                secureTextEntry={!showPassword}
                            />
                            <TouchableOpacity style={styles.eyeIcon} onPress={() => setShowPassword(!showPassword)}>
                                <MaterialIcons name={showPassword ? "visibility-off" : "visibility"} size={22} color="#64748b" />
                            </TouchableOpacity>
                        </View>

                        <TouchableOpacity
                            style={styles.button}
                            onPress={handleRegister}
                            disabled={loading}
                        >
                            {loading ? (
                                <ActivityIndicator color="white" />
                            ) : (
                                <Text style={styles.buttonText}>Register & Get Started</Text>
                            )}
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.loginLink} onPress={() => router.back()}>
                            <Text style={styles.loginText}>Already have an account? <Text style={styles.linkText}>Login</Text></Text>
                        </TouchableOpacity>
                    </View>
                </View>
            </ScrollView>
        </KeyboardAvoidingView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#f8fafc',
    },
    scroll: {
        flexGrow: 1,
        justifyContent: 'center',
        padding: 20,
    },
    card: {
        backgroundColor: 'white',
        borderRadius: 20,
        padding: 25,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 10 },
        shadowOpacity: 0.1,
        shadowRadius: 20,
        elevation: 5,
    },
    backBtn: {
        marginBottom: 20,
    },
    header: {
        alignItems: 'center',
        marginBottom: 25,
    },
    title: {
        fontSize: 28,
        fontWeight: '900',
        color: '#dc2626',
    },
    subText: {
        color: '#64748b',
        marginTop: 5,
    },
    form: {
        gap: 12,
    },
    label: {
        fontWeight: '600',
        color: '#1e293b',
        fontSize: 14,
        marginBottom: 2,
    },
    input: {
        borderWidth: 1,
        borderColor: '#e2e8f0',
        borderRadius: 12,
        padding: 15,
        fontSize: 16,
        backgroundColor: '#f1f5f9',
    },
    passwordContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        borderWidth: 1,
        borderColor: '#e2e8f0',
        borderRadius: 12,
        backgroundColor: '#f1f5f9',
    },
    passwordInput: {
        flex: 1,
        padding: 15,
        fontSize: 16,
    },
    eyeIcon: {
        paddingHorizontal: 15,
    },
    button: {
        backgroundColor: '#dc2626',
        borderRadius: 12,
        padding: 18,
        alignItems: 'center',
        marginTop: 15,
    },
    buttonText: {
        color: 'white',
        fontWeight: '800',
        fontSize: 16,
    },
    loginLink: {
        marginTop: 15,
        alignItems: 'center',
    },
    loginText: {
        color: '#64748b',
        fontSize: 14,
    },
    linkText: {
        color: '#dc2626',
        fontWeight: '700',
    }
});
