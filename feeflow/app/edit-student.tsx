import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, TextInput, TouchableOpacity, ScrollView, Alert, ActivityIndicator } from 'react-native';
import { useRouter, Stack, useLocalSearchParams } from 'expo-router';
import { getClasses, updateStudent, getStudents } from '../lib/api';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';

export default function EditStudentScreen() {
    const { id } = useLocalSearchParams();
    const [name, setName] = useState('');
    const [classId, setClassId] = useState('');
    const [rollNo, setRollNo] = useState('');
    const [phone, setPhone] = useState('');
    const [parent, setParent] = useState('');
    const [session, setSession] = useState('');
    const [classes, setClasses] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const router = useRouter();

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const [cResp, sResp] = await Promise.all([getClasses(), getStudents()]);
            if (cResp.status) setClasses(cResp.data);

            const student = sResp.data.find((s: any) => s.id.toString() === id);
            if (student) {
                setName(student.name);
                setClassId(student.class_id.toString());
                setRollNo(student.roll_no);
                setPhone(student.phone);
                setParent(student.parent_name);
                setSession(student.session);
            }
        } catch (e) {
            console.error(e);
        } finally {
            setLoading(false);
        }
    };

    const handleUpdate = async () => {
        if (!name || !classId) {
            Alert.alert('Error', 'Name and Class are required');
            return;
        }

        setSaving(true);
        try {
            const response = await updateStudent(id, {
                name,
                class_id: classId,
                roll_no: rollNo,
                phone,
                parent_name: parent,
                session
            });

            if (response.status) {
                Alert.alert('Success', 'Student Updated Successfully', [
                    { text: 'OK', onPress: () => router.back() }
                ]);
            } else {
                Alert.alert('Error', response.message);
            }
        } catch (e) {
            Alert.alert('Error', 'Failed to update student');
        } finally {
            setSaving(false);
        }
    };

    if (loading) return <ActivityIndicator size="large" color="#dc2626" style={{ marginTop: 50 }} />;

    return (
        <SafeAreaView style={styles.container} edges={['left', 'top', 'right']}   >
            <Stack.Screen options={{ title: 'Edit Student', headerShown: true }} />

            <ScrollView showsVerticalScrollIndicator={false}>
                <View style={styles.form}>
                    <Text style={styles.label}>Full Name *</Text>
                    <TextInput
                        style={styles.input}
                        placeholder="Student Name"
                        value={name}
                        onChangeText={setName}
                    />

                    <Text style={styles.label}>Select Class/Course *</Text>
                    <View style={styles.classGrid}>
                        {classes.map((c) => (
                            <TouchableOpacity
                                key={c.id}
                                style={[styles.classItem, classId === c.id.toString() && styles.classSelected]}
                                onPress={() => setClassId(c.id.toString())}
                            >
                                <Text style={[styles.classText, classId === c.id.toString() && styles.classTextSelected]}>
                                    {c.class_name}
                                </Text>
                            </TouchableOpacity>
                        ))}
                    </View>

                    <View style={styles.row}>
                        <View style={{ flex: 1 }}>
                            <Text style={styles.label}>Roll No</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="001"
                                value={rollNo}
                                onChangeText={setRollNo}
                            />
                        </View>
                        <View style={{ flex: 1 }}>
                            <Text style={styles.label}>Session</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="2024-25"
                                value={session}
                                onChangeText={setSession}
                            />
                        </View>
                    </View>

                    <Text style={styles.label}>Phone Number</Text>
                    <TextInput
                        style={styles.input}
                        placeholder="9988776655"
                        value={phone}
                        onChangeText={setPhone}
                        keyboardType="phone-pad"
                    />

                    <Text style={styles.label}>Parent/Guardian Name</Text>
                    <TextInput
                        style={styles.input}
                        placeholder="Father/Mother Name"
                        value={parent}
                        onChangeText={setParent}
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
                                <Text style={styles.buttonText}>Save Changes</Text>
                            </>
                        )}
                    </TouchableOpacity>
                </View>
            </ScrollView>
        </SafeAreaView>
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
    row: {
        flexDirection: 'row',
        gap: 15,
    },
    classGrid: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        gap: 10,
    },
    classItem: {
        paddingVertical: 10,
        paddingHorizontal: 15,
        borderRadius: 10,
        backgroundColor: '#f1f5f9',
        borderWidth: 1,
        borderColor: '#e2e8f0',
    },
    classSelected: {
        backgroundColor: '#dc2626',
        borderColor: '#dc2626',
    },
    classText: {
        color: '#475569',
        fontWeight: '600',
    },
    classTextSelected: {
        color: '#fff',
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
