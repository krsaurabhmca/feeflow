import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, TextInput, TouchableOpacity, ScrollView, Alert, ActivityIndicator } from 'react-native';
import { useRouter, Stack } from 'expo-router';
import { getProfile, updateProfile } from '../lib/api';
import { Ionicons } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store';

export default function UpdateProfileScreen() {
    const [name, setName] = useState('');
    const [phone, setPhone] = useState('');
    const [address, setAddress] = useState('');
    const [recognition, setRecognition] = useState('');
    const [affiliation, setAffiliation] = useState('');
    const [prefix, setPrefix] = useState('');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const router = useRouter();

    useEffect(() => {
        loadProfile();
    }, []);

    const loadProfile = async () => {
        try {
            const resp = await getProfile();
            if (resp.status) {
                const p = resp.data;
                setName(p.name);
                setPhone(p.phone || '');
                setAddress(p.address || '');
                setRecognition(p.recognition_text || '');
                setAffiliation(p.affiliation_text || '');
                setPrefix(p.receipt_prefix || '');
            }
        } catch (e) {
            console.error(e);
        } finally {
            setLoading(false);
        }
    };

    const handleUpdate = async () => {
        if (!name) {
            Alert.alert('Error', 'Institute Name is required');
            return;
        }

        setSaving(true);
        try {
            const response = await updateProfile({
                name,
                phone,
                address,
                recognition_text: recognition,
                affiliation_text: affiliation,
                receipt_prefix: prefix
            });

            if (response.status) {
                await SecureStore.setItemAsync('institute_name', name);
                Alert.alert('Success', 'Profile Updated Successfully');
                router.back();
            } else {
                Alert.alert('Error', response.message);
            }
        } catch (e) {
            Alert.alert('Error', 'Failed to update profile');
        } finally {
            setSaving(false);
        }
    };

    if (loading) return <ActivityIndicator size="large" color="#dc2626" style={{ marginTop: 50 }} />;

    return (
        <ScrollView style={styles.container}>
            <Stack.Screen options={{ title: 'Update Profile', headerShown: true }} />

            <View style={styles.form}>
                <Text style={styles.label}>Institute Name *</Text>
                <TextInput
                    style={styles.input}
                    placeholder="e.g. Oakridge Public School"
                    value={name}
                    onChangeText={setName}
                />

                <Text style={styles.label}>Phone Number</Text>
                <TextInput
                    style={styles.input}
                    placeholder="Contact Number"
                    value={phone}
                    onChangeText={setPhone}
                    keyboardType="phone-pad"
                />

                <Text style={styles.label}>Address</Text>
                <TextInput
                    style={[styles.input, { height: 80 }]}
                    placeholder="Full Address"
                    value={address}
                    onChangeText={setAddress}
                    multiline
                />

                <Text style={styles.label}>Recognition / Affiliation Text</Text>
                <TextInput
                    style={styles.input}
                    placeholder="e.g. Recognized by Govt of UP"
                    value={recognition}
                    onChangeText={setRecognition}
                />

                <Text style={styles.label}>Affiliation No (Optional)</Text>
                <TextInput
                    style={styles.input}
                    placeholder="e.g. Affiliation No: 2130000"
                    value={affiliation}
                    onChangeText={setAffiliation}
                />

                <Text style={styles.label}>Receipt Prefix</Text>
                <TextInput
                    style={styles.input}
                    placeholder="e.g. FF-2024-"
                    value={prefix}
                    onChangeText={setPrefix}
                />

                <TouchableOpacity
                    style={[styles.button, saving && { opacity: 0.7 }]}
                    onPress={handleUpdate}
                    disabled={saving}
                >
                    {saving ? (
                        <ActivityIndicator color="white" />
                    ) : (
                        <>
                            <Ionicons name="save-outline" size={20} color="white" style={{ marginRight: 10 }} />
                            <Text style={styles.buttonText}>Save Profile</Text>
                        </>
                    )}
                </TouchableOpacity>
            </View>
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#fff',
    },
    form: {
        padding: 20,
        gap: 15,
    },
    label: {
        fontWeight: '700',
        color: '#1e293b',
        fontSize: 14,
    },
    input: {
        borderWidth: 1,
        borderColor: '#e2e8f0',
        borderRadius: 12,
        padding: 15,
        fontSize: 16,
        backgroundColor: '#f8fafc',
    },
    button: {
        backgroundColor: '#dc2626',
        borderRadius: 12,
        padding: 18,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: 20,
    },
    buttonText: {
        color: 'white',
        fontWeight: '800',
        fontSize: 16,
    },
});
